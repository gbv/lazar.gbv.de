<?php declare(strict_types=1);

require '../../vendor/autoload.php';

// initialize proxy
$config = json_decode(file_get_contents('../../config-oai.json'), true);
$proxy = new GBV\OAI\Proxy($config);

// handle request
$request = GuzzleHttp\Psr7\ServerRequest::fromGlobals();
$response = $proxy($request)->wait();

// emit response
header_remove();

foreach ($response->getHeaders() as $key => $value) {
    if ($key != 'Transfer-Encoding') {
        header("$key: $value[0]");
    }
}

http_response_code($response->getStatusCode());
echo $response->getBody();
