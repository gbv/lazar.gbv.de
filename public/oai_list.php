<?php

require_once '../../vendor/autoload.php';

function show_oai_list($set)
{
    $config = json_decode(file_get_contents('../../config-oai.json'), true);
    $proxy = new GBV\OAI\Proxy($config);

    $query = "?verb=ListRecords&metadataPrefix=easydb&set=$set";
    $request = new GuzzleHttp\Psr7\Request('GET', "http://example.org/oai$query");

    $response = $proxy($request)->wait();
    $xml = simplexml_load_string($response->getBody());

    echo "<ul>";
    foreach ($xml->ListRecords->record as $record) {
        $id = (string)$record->header->identifier;
        $id = preg_replace('/^.*:/', '', $id);
        print "<li><a href='../id/$id'>$id</li>";
        # TODO: show name via <easydb:_standard>
    }
    echo "</ul>";
}
