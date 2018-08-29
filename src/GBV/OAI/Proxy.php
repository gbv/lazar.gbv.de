<?php declare(strict_types=1);

namespace GBV\OAI;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use SimpleXMLElement;
use DOMDocument;

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
        $body = $this->transformBody((string)$response->getBody(), $query);
        $response = $response->withBody(Psr7\stream_for($body));

        return \GuzzleHttp\Promise\promise_for($response);
    }

    public function transformBody(string $body, array $query): string
    {
        // add processing instructions
        if (count($this->instructions)) {
            $pis = [];
            foreach ($this->instructions as $name => $content) {
                $pis[] = "<?$name $content?>";
            }
            $body = preg_replace("/\?>$/m", "?>\n".implode("\n", $pis), $body);
        }

        // rewrite base URL
        $body = preg_replace(
            "/>[^<]*<\/request>/m",
            ">{$this->baseUrl}</request>",
            $body
        );

        $verb = $query['verb'] ?? '';

        if (count($query['sets']) > 1 && $verb == 'ListRecords') {
            $xml = new SimpleXMLElement($body);
            $xml = $this->filterListRecords($xml, $query);
            $body = $xml->asXML();
        }

        if ($verb == 'ListMetadataFormats' && count($this->formats)) {
            $xml = new SimpleXMLElement($body);
            $xml = $this->filterMetadataFormats($xml);
            $body = $xml->asXML();
        }

        // pretty-print XML (requires to serialize and parse again)
        if ($this->pretty) {
            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($body);
            $body = $dom->saveXML();
        }

        return $body;
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

        return $query;
    }

    public function extendFormat($node, $format)
    {
        if ($format['schema'] ?? 0) {
            if (!$node->{'schema'}) {
                $node->addChild('schema');
            }
            $node->{'schema'} = $format['schema'];
        }
        if ($format['namespace'] ?? 0) {
            if (!$node->{'metadataNamespace'}) {
                $node->addChild('metadataNamespace');
            }
            $node->{'metadataNamespace'} = $format['namespace'];
        }
    }

    public function filterMetadataFormats(SimpleXMLElement $xml)
    {
        $xml->registerXPathNamespace('oai', static::OAI_NS);

        $formats = $this->formats ?? [];

        // extend existing format description
        $formatNodes = $xml->xpath('//oai:metadataFormat');
        foreach ($formatNodes as $node) {
            $name = (string)$node->{'metadataPrefix'};
            if (isset($formats[$name])) {
                $format = $formats[$name];
                $this->extendFormat($node, $format);
                unset($formats[$name]);
            }
        }

        // add formats
        $root = $xml->xpath('//oai:ListMetadataFormats')[0];
        foreach ($formats as $name => $format) {
            $node = $root->addChild('metadataFormat');
            $node->addChild('metadataPrefix', $name);
            $this->extendFormat($node, $format);
        }

        return $xml;
    }

    public function filterListRecords(SimpleXMLElement $xml, array $query): SimpleXMLElement
    {
        $xml->registerXPathNamespace('oai', static::OAI_NS);

        // filter records with sets
        $sets = $query['sets'];
        if (count($sets) > 1) {
            $records = $xml->xpath('//oai:record');
            foreach ($records as $rec) {
                $rec->registerXPathNamespace('oai', static::OAI_NS);
                $setSpecs = array_map(
                    function ($setSpec) {
                        return (string)$setSpec;
                    },
                    $rec->xpath('oai:header/oai:setSpec')
                );
                if (array_intersect($sets, $setSpecs) != $sets) {
                    unset($rec[0][0]);
                }
            }
        }

        return $xml;
    }
}
