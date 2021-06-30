<?php

namespace alcamo\http;

use PHPUnit\Framework\TestCase;

class SapiEmitterTest extends TestCase
{
    /**
     * @dataProvider emitProvider
     */
    public function testEmit($type, $text, $sendContentLength, $expectedOutput)
    {
        exec(
            'PHPUNIT_COMPOSER_INSTALL="' . PHPUNIT_COMPOSER_INSTALL . '" php '
            . __DIR__ . DIRECTORY_SEPARATOR
            . "SapiEmitterAux.php $type '$text' $sendContentLength",
            $output
        );

        $this->assertSame($expectedOutput, implode('', $output));
    }

    public function emitProvider()
    {
        return [
            'text-without-content-length' => [ 'text', 'foo', 0, 'foo' ],
            'text-with-content-length' => [ 'text', 'foo', 1, 'foo' ],
            'pipe-without-content-length' => [ 'pipe', 'echo foo', 0, 'foo' ],
            'pipe-with-content-length' => [ 'pipe', 'echo foo', 1, 'foo' ]
        ];
    }
}
