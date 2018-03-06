<?php declare(strict_types=1);

namespace GBV;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7;
use GuzzleHttp\Client;

/**
 * OAI-PMH Proxy implemented as Guzzle Handler.
 */
class OAIProxy
{
    protected $backend;
    protected $baseUrl;

    public function __construct(array $config)
    {
        $this->backend = $config['backend'];
        $this->baseUrl = $config['baseUrl'];
        $this->client  = $config['client'] ?? new Client([
            'http_errors' => false
        ]);
    }

    public function __invoke(RequestInterface $request)
    {
        $query = $request->getUri()->getQuery();

        $headers = $request->getHeaders();
        $headers = array_diff_key($headers, array_flip(['Cookie', 'Host']));

        $response = $this->client->request(
            $request->getMethod(),
            $this->backend,
            ['headers' => $headers, 'query' => $query]
        );

        $body = $this->transform((string)$response->getBody());
        $response = $response->withBody(Psr7\stream_for($body));

        return \GuzzleHttp\Promise\promise_for($response);
    }

    public function transform(string $body)
    {
        $body = preg_replace(
            "/<request>[^<]*</m",
            "<request>{$this->baseUrl}<",
            $body
        );

/*

$verb = $query['verb'] ?? '';

// split sets if multiple sets provided as comma-separated list
$sets = explode(',', $query['set'] ?? '');
if (count($sets) > 1) {
    $query['set'] = $sets[0];
}

// helper function to filter and transform records
function filter_body($body)
{
    global $sets;
    $xml = new SimpleXMLElement($body);

    // TODO: filter records with sets
    // TODO: transform with XSLT if requested

    return $xml->asXML();
}

*/

        return $body;
    }
}
