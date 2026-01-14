<?php

namespace alcamo\http;

use PHPUnit\Framework\TestCase;
use alcamo\rdfa\RdfaData;

class ResponseTest extends TestCase
{
    public function testConstruct(): void
    {
        $bodyText = '{ "msg": "Hello, World!" }';

        $rdfaData = RdfaData::newFromIterable(
            [ 'dc:format', 'application/json' ]
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
    ): void {
        $response = Response::newFromStatusAndText($status, $text, $rdfaData);

        $this->assertSame($status, $response->getStatusCode());
        $this->assertSame($expectedText, (string)$response->getBody());

        $this->assertEquals(
            RdfaData::newFromIterable($expectedRdfaData),
            $response->getRdfaData()
        );
    }

    public function newFromStatusAndTextProvider(): array
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
    public function testEmit($type, $text, $expectedOutput): void
    {
        exec(
            'PHPUNIT_COMPOSER_INSTALL="' . PHPUNIT_COMPOSER_INSTALL . '" php '
            . __DIR__ . DIRECTORY_SEPARATOR
            . "ResponseEmitAux.php $type '$text'",
            $output
        );

        $this->assertSame($expectedOutput, implode('', $output));
    }

    public function emitProvider(): array
    {
        return [
            'text' => [ 'text', 'foo', 'foo' ],
            'pipe' => [ 'pipe', 'echo foo', 'foo' ]
        ];
    }
}
