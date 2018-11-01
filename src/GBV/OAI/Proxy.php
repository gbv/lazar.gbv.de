<?php declare(strict_types=1);

namespace GBV\OAI;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use DOMDocument;
use DOMElement;
use DOMXPath;
use DOMNodeList;
use DOMComment;
use GBV\XSLTPipeline;

/**
 * OAI-PMH Proxy implemented as Guzzle Handler.
 *
 * The proxy extends an OAI-PMH service with
 *
 * - rewriting of the baseURL
 * - injection of processing instructions, especially XSLT
 * - support of set intersection (only for ListRecords)
 * - additional metadata formats
 * - optional pretty-printing
 *
 * See <https://packagist.org/packages/picturae/oai-pmh> for a full
 * OAI-PMH server implementation in PHP.
 */
class Proxy
{
    // only these HTTP query arguments are passed to the OAI-PMH backend
    const OAI_ARGUMENTS = [
        'verb', 'identifier', 'metadataPrefix',
        'from', 'until', 'set', 'resumptionToken'
    ];

    const OAI_NS = 'http://www.openarchives.org/OAI/2.0/';

    public function __construct(array $config)
    {
        $this->backend = $config['backend'];  // required
        $this->baseUrl = $config['baseUrl'];  // required
        $this->client  = $config['client'] ?? new Client([
            'http_errors' => false
        ]);

        // processing instructions
        $this->instructions = $config['instructions'] ?? [];
        if ($config['xslt'] ?? false) {
            $this->instructions['xml-stylesheet'] =
                'type="text/xsl" href="'.$config['xslt'].'"';
        }

        $this->formats = $config['formats'] ?? [];
        $this->pretty = $config['pretty'] ?? true;
    }

    public function __invoke(RequestInterface $request)
    {
        $headers = $request->getHeaders();
        $headers = array_diff_key($headers, array_flip(['Cookie', 'Host']));
        
        parse_str($request->getUri()->getQuery(), $query);
        $query = $this->transformQuery($query);

        // pass query to OAI-PMH backend
        $args = array_intersect_key($query, array_flip(static::OAI_ARGUMENTS));
        $response = $this->client->request(
            $request->getMethod(),
            $this->backend,
            [ 'headers' => $headers, 'query' => $args ]
        );
        # error_log($this->backend.'?'.http_build_query($args));

        // transform response and return as Promise
        $dom = $this->transformBody((string)$response->getBody(), $query);
        $response = $response->withBody(Psr7\stream_for($dom->saveXML()));

        return \GuzzleHttp\Promise\promise_for($response);
    }

    public function getRecord(string $format, string $id)
    {
        $query = $this->transformQuery([
            'verb' => 'GetRecord',
            'metadataPrefix' => $format,
            'identifier' => $id
        ]);
        $args = array_intersect_key($query, array_flip(static::OAI_ARGUMENTS));
        $response = $this->client->request('GET', $this->backend, [ 'query' => $args, ]);

        $dom = $this->transformBody((string)$response->getBody(), $query);

        return static::xpath($dom, '//oai:GetRecord/oai:record/oai:metadata/*')[0];
    }

    public function transformBody(string $body, array $query): DOMDocument
    {
        $dom = new DOMDocument();
        $dom->loadXML($body); // TODO: catch error

        $prefix = $query['targetPrefix'] ?? '';
        $verb = $query['verb'] ?? '';

        if ($verb == 'ListRecords') {
            if (count($query['sets']) > 1) {
                $dom = $this->filterListRecords($dom, $query);
            }
            $dom = $this->rewriteRecords($dom, $prefix);
        } elseif ($verb == 'GetRecord') {
            $dom = $this->rewriteRecords($dom, $prefix);
        } elseif ($verb == 'ListMetadataFormats' && count($this->formats)) {
            $dom = $this->rewriteMetadataFormats($dom);
        }

        // add processing instructions
        foreach ($this->instructions as $name => $content) {
            $pi = $dom->createProcessingInstruction($name, $content);
            $dom->insertBefore($pi, $dom->documentElement);
        }

        // rewrite request element
        foreach (static::xpath($dom, '//oai:request') as $node) {
            $node->textContent = $this->baseUrl;
            if ($prefix && $node->getAttribute('metadataPrefix')) {
                $node->setAttribute('metadataPrefix', $query['targetPrefix']);
            }
        }

        // enforce pretty-printing
        if ($this->pretty) {
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
        }
 
        return $dom;
    }

    // helper function
    public static function xpath($node, string $query): DOMNodeList
    {
        $dom = $node instanceof DOMDocument ? $node : $node->ownerDocument;
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('oai', static::OAI_NS);
        return $xpath->query($query, $node);
    }

    // helper function
    public static function xmlChild(DOMElement $node, string $name, bool $create = false)
    {
        foreach ($node->childNodes as $child) {
            if ($child->nodeName == $name) {
                return $child;
            }
        }
        return $create ? $node->appendChild(new DOMElement($name)) : null;
    }


    public function transformQuery(array $query): array
    {
        // split sets if multiple sets provided as comma-separated list
        if (isset($query['set'])) {
            $sets = explode(',', $query['set']);
            if (count($sets) > 1) {
                $query['set'] = $sets[0];
            }
            $query['sets'] = $sets;
        } else {
            $query['sets'] = [];
        }

        // change metadata prefix to apply pipeline on
        $prefix = $query['metadataPrefix'] ?? '';
        $format = $this->formats[$prefix] ?? [];
        $pipeline = $format['pipeline'] ?? null;
        if ($pipeline) {
            $query['metadataPrefix'] = $pipeline[0];
            $query['targetPrefix'] = $prefix;
        } else {
            unset($query['targetPrefix']);
        }

        return $query;
    }

    public function extendFormat(DOMElement $node, $format)
    {
        if ($format['schema'] ?? 0) {
            $elem = static::xmlChild($node, 'schema', true);
            $elem->textContent = $format['schema'];
        }
        if ($format['namespace'] ?? 0) {
            $elem = static::xmlChild($node, 'metadataNamespace', true);
            $elem->textContent = $format['namespace'];
        }
    }

    public function rewriteMetadataFormats(DOMDocument $dom): DOMDocument
    {
        $formats = $this->formats;

        // extend existing format description
        foreach (static::xpath($dom, '//oai:metadataFormat') as $node) {
            $name = static::xmlChild($node, 'metadataPrefix');
            $name && $name = $name->textContent;
            if (isset($formats[$name])) {
                $format = $formats[$name];
                $this->extendFormat($node, $format);
                unset($formats[$name]);
            }
        }

        // add formats
        $root = static::xpath($dom, '//oai:ListMetadataFormats')->item(0);
        foreach ($formats as $name => $format) {
            $node = $root->appendChild(new DOMElement('metadataFormat'));
            $prefix = $node->appendChild(new DOMElement('metadataPrefix'));
            $prefix->textContent = $name;
            $this->extendFormat($node, $format);
        }

        return $dom;
    }

    public function rewriteRecords(DOMDocument $dom, string $prefix)
    {
        $format = $this->formats[$prefix] ?? null;

        $pipeline = new XSLTPipeline();
        $pipeline->appendFiles(array_slice($format['pipeline'] ?? [], 1));

        foreach (static::xpath($dom, '//oai:record') as $record) {
            foreach (static::xpath($record, 'oai:metadata/*') as $metadata) {
                $metadata->setAttribute('xmlns:'.$metadata->prefix, $metadata->namespaceURI);
                // file_put_contents('/tmp/tmp.xml', $dom->saveXML($metadata));

                // move metadata to a new document (why?)
                $m = new DOMDocument();
                $m->appendChild($m->importNode($metadata, true));

                $result = $pipeline->transformToDoc($m);
                if ($result->documentElement) {
                    $node = $dom->importNode($result->documentElement, true);
                    $metadata->parentNode->replaceChild($node, $metadata);
                } else {
                    // remove the whole record
                    $comment = new DOMComment("skipped record not available in $prefix format");
                    $record->parentNode->replaceChild($comment, $record);
                }
            }
        }

        return $dom;
    }


    public function filterListRecords(DOMDocument $dom, array $query): DOMDocument
    {
        // filter records with sets
        $sets = $query['sets'];
        foreach (static::xpath($dom, '//oai:record') as $rec) {
            $setSpecs = [];
            foreach (static::xpath($rec, 'oai:header/oai:setSpec') as $setSpec) {
                $setSpecs[] = $setSpec->textContent;
            }
            if (array_intersect($sets, $setSpecs) != $sets) {
                $rec->parentNode->removeChild($rec);
            }
        }

        return $dom;
    }
}
