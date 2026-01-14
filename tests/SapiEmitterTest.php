<?php

namespace alcamo\http;

use PHPUnit\Framework\TestCase;

class SapiEmitterTest extends TestCase
{
    /**
     * @dataProvider emitProvider
     */
    public function testEmit($type, $text, $expectedOutput)
    {
        exec(
            'PHPUNIT_COMPOSER_INSTALL="' . PHPUNIT_COMPOSER_INSTALL . '" php '
            . __DIR__ . DIRECTORY_SEPARATOR
            . "SapiEmitterAux.php $type '$text'",
            $output
        );

        $this->assertSame($expectedOutput, implode('', $output));
    }

    public function emitProvider()
    {
        return [
            'text' => [ 'text', 'foo', 'foo' ],
            'pipe' => [ 'pipe', 'echo foo', 'foo' ]
        ];
    }
}
