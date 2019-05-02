<?php

use GBV\OAI\Proxy;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use PHPUnit\Framework\TestCase;

class OAIProxyTest extends TestCase
{
    protected $proxy;

    public static function mockClient(string $file)
    {
        $response = new Response(
            200,
            ['Content-Type'=>'text/xml; charset=utf-8'],
            fopen("tests/$file.xml", 'r')
        );
        $mock = new MockHandler([ $response ]);
        $handler = HandlerStack::create($mock);
        
        return new Client(['handler' => $handler]);
    }

    protected function request(string $query, string $file)
    {
        $proxy = $this->proxy;
        $proxy->client = static::mockClient($file);

        $req = new Request('GET', "http://example.org/oai$query");
        $res = $proxy($req)->wait();
        return (string)$res->getBody();
    }

    public function testCore()
    {
        $this->proxy = new Proxy([
            'backend' => 'http://example.com/',
            'baseUrl' => 'http://example.org/',
        ]);
        
        $body = $this->request('', 'badVerb');

        $this->assertRegExp('/<error code="badVerb">/m', $body);
        $this->assertRegExp('/<request>http:\/\/example.org\/</m', $body);
    }

    public function testFormats()
    {
        $this->proxy = new Proxy([
            'backend' => 'http://example.com/',
            'baseUrl' => 'http://example.org/',
            'pretty' => 1,
            'formats' => [
                'example' => [
                    'schema' => 'http://examle.org/schema',
                    'namespace' => 'http://example.org/ns'
                ],
                'another' => [
                    'schema' => 'http://examle.org/another/schema',
                    'namespace' => 'http://example.org/another/ns'
                ]
            ]
        ]);

        $body = $this->request('?verb=ListMetadataFormats', 'listFormats');

        $got = simplexml_load_string($body);
        $expect = simplexml_load_file('tests/listExtendedFormats.xml');

        $this->assertEquals($expect, $got);
    }
}
