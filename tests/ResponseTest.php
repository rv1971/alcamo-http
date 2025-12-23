<?php

namespace alcamo\http;

use PHPUnit\Framework\TestCase;
use alcamo\rdfa\RdfaData;

class ResponseTest extends TestCase
{
    public function testConstruct()
    {
        $bodyText = '{ "msg": "Hello, World!" }';

        $rdfaData = RdfaData::newFromIterable(
            [
                'dc:format' => 'application/json'
            ]
        );

        $response = new Response($rdfaData);

        $response->getBody()->write($bodyText);

        $this->assertSame($rdfaData, $response->getRdfaData());

        $this->assertSame($bodyText, (string)$response->getBody());

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * @dataProvider newFromStatusAndTextProvider
     */
    public function testNewFromStatusAndText(
        $status,
        $text,
        $rdfaData,
        $expectedText,
        $expectedRdfaData
    ) {
        $response = Response::newFromStatusAndText($status, $text, $rdfaData);

        $this->assertSame($status, $response->getStatusCode());
        $this->assertSame($expectedText, (string)$response->getBody());

        $this->assertEquals(
            RdfaData::newFromIterable($expectedRdfaData),
            $response->getRdfaData()
        );
    }

    public function newFromStatusAndTextProvider()
    {
        return [
            'simple' => [
                404,
                null,
                null,
                'Not Found',
                [ [ 'dc:format', 'text/plain' ] ]
            ],
            'text-and-array' => [
                200,
                'Lorem ipsum',
                [ [ 'dc:format', 'text/plain; charset=us-ascii' ] ],
                'Lorem ipsum',
                [ [ 'dc:format', 'text/plain; charset=us-ascii' ] ]
            ],
            'text-and-rdfa' => [
                200,
                'Lorem ipsum',
                RdfaData::newFromIterable(
                    [ [ 'dc:format', 'text/plain; charset=us-ascii' ] ]
                ),
                'Lorem ipsum',
                [ [ 'dc:format', 'text/plain; charset="us-ascii"' ] ]
            ]
        ];
    }

    /**
     * @dataProvider emitProvider
     */
    public function testEmit($type, $text, $sendContentLength, $expectedOutput)
    {
        exec(
            'PHPUNIT_COMPOSER_INSTALL="' . PHPUNIT_COMPOSER_INSTALL . '" php '
            . __DIR__ . DIRECTORY_SEPARATOR
            . "ResponseEmitAux.php $type '$text' $sendContentLength",
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
