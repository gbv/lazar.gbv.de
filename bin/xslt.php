#!/usr/bin/env php
<?php

require dirname(__FILE__).'/../vendor/autoload.php';

use GBV\XSLTPipeline;

$doc = new DOMDocument();
$doc->load($argv[1]);

$pipeline = new XSLTPipeline();
$pipeline->appendFiles(array_slice($argv, 2));

$doc = $pipeline->transformToDoc($doc);

$doc->preserveWhiteSpace = false;
$doc->formatOutput = true;
echo $doc->saveXML();
