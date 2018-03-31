<?php declare(strict_types=1);

namespace GBV\OAI;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use SimpleXMLElement;

/**
 * OAI-PMH Proxy implemented as Guzzle Handler.
 *
 * The proxy extends an OAI-PMH service with
 *
 * - rewriting of the baseURL
 * - injection of processing instructions, especially XSLT
 * - support of set intersection (only for ListRecords)
 * - additional metadata formats (not implemented yet)
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
            $body = $this->filterXML($xml, $query)->asXML();
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

    public function filterXML(SimpleXMLElement $xml, array $query): SimpleXMLElement
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
