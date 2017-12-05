<?php

namespace App\Tests\Service;
;
use App\Service\Canonicalizer;
use PHPUnit\Framework\TestCase;

class CanonicalizerTest extends TestCase
{
    /**
     * @dataProvider canonicalizeProvider
     *
     * @param mixed $source
     * @param mixed $expectedResult
     */
    public function testCanonicalize($source, $expectedResult)
    {
        $canonicalizer = new Canonicalizer();
        $this->assertSame($expectedResult, $canonicalizer->canonicalize($source));
    }

    /**
     * @return array
     */
    public function canonicalizeProvider()
    {
        return [
            [null, null],
            ['FOO', 'foo'],
            [chr(171), PHP_VERSION_ID < 50600 ? chr(171) : '?'],
        ];
    }
}
