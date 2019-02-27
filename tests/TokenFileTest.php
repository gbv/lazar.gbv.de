<?php

use GBV\OAI\TokenFile;
use PHPUnit\Framework\TestCase;

class TokenFileTest extends TestCase
{
    function testTokenFile() {
        $file = new TokenFile(tempnam(sys_get_temp_dir(), 'oai-test'), 2);

        $this->assertEquals($file->get('abc'), null);

        $file->add('abc', ['a'=>1,'b'=>2]);
        $this->assertEquals($file->get('abc'), ['a'=>1,'b'=>2]);

        $file->add('xyz', ['x'=>3,'y'=>4]);
        $this->assertEquals($file->get('xyz'), ['x'=>3,'y'=>4]);
        $this->assertEquals($file->get('abc'), ['a'=>1,'b'=>2]);

        if (FALSE) {
          sleep(3);
          $this->assertEquals($file->get('abc'), null);
        }
    }
}
