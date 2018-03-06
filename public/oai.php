<?php

require '../vendor/autoload.php';

use GuzzleHttp\Client;

// Get request headers from client (minus headers to hide)
$headers = array_diff_key(getallheaders(), array_flip(['Cookie', 'Host']));
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Instantiate HTTP Client
$client = new Client();

$url = 'https://lazardb.gbv.de/api/plugin/base/oai/oai/request';
$query = $_GET;

// split sets if multiple sets provided as comma-separated list
$sets = explode(',', $query['set'] ?? '');
if (count($sets) > 1) {
    $query['set'] = $sets[0];
}

// helper function to filter and transform records
function filter_body($body) {
    global $sets;
    $xml = new SimpleXMLElement($body);

    // TODO: filter records with sets
    // TODO: transform with XSLT if requested

    return $xml->asXML();
}

// query backend OAI-PMH
$body = '';
try {
    error_log('query backend');

    $options = ['headers' => $headers, 'query' => $query];
    $response = $client->request($method, $url, $options);
    $body = $response->getBody();

    if (count($sets) > 1) {
        error_log('filter response');
        $body = filter_body($body);
    }

} catch (RequestException $e) {
    if ($e->hasResponse()) {
        $response = $e->getResponse();
    }
} catch (ServerException $e) {
    if ($e->hasResponse()) {
        $response = $e->getResponse();
    }
}

error_log('send response');

// Emit all headers except chunked encoding header
header_remove();
foreach ($response->getHeaders() as $key => $value) {
    if ($key != 'Transfer-Encoding') {
        header("$key: $value[0]");
    }
}

// Emit Status code and body
http_response_code($response->getStatusCode());
echo $body;
