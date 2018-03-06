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
    protected static function xmlResponse(string $file)
    {
        return new Response(
            200,
            ['Content-Type'=>'text/xml; charset=utf-8'],
            fopen("tests/$file.xml", 'r')
        );
    }

    public function testCore()
    {
        $mock = new MockHandler([ static::xmlResponse('badVerb') ]);
        $handler = HandlerStack::create($mock);
        $proxy = new Proxy([
            'backend' => 'http://example.com/',
            'baseUrl' => 'http://example.org/',
            'client' => new Client(['handler' => $handler]),
        ]);

        $req = new Request('GET', 'http://example.org/');
        $res = $proxy($req)->wait();
        $body = (string)$res->getBody();

        $this->assertRegExp('/<error code="badVerb">/m', $body);
        $this->assertRegExp('/<request>http:\/\/example.org\/</m', $body);
    }
}
