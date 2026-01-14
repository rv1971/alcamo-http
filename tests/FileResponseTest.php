<?php

namespace alcamo\http;

use alcamo\rdfa\{MediaType, RdfaData};
use PHPUnit\Framework\TestCase;

class FileResponseTest extends TestCase
{
    public function testNewFromPath(): void
    {
        $response = FileResponse::newFromPath(__FILE__);

        $this->assertSame(
            file_get_contents(__FILE__),
            (string)$response->getBody()
        );

        $this->assertSame(
            filesize(__FILE__),
            (int)(string)$response->getRdfaData()['http:content-length']
                ->first()
        );

        $this->assertEquals(
            MediaType::newFromFilename(__FILE__),
            $response->getRdfaData()['dc:format']->first()->getObject()
        );

        $response = FileResponse::newFromPath(
            __FILE__,
            [ [ 'dc:format', 'application/octet-stream' ] ]
        );

        $this->assertSame(
            file_get_contents(__FILE__),
            (string)$response->getBody()
        );

        $this->assertSame(
            filesize(__FILE__),
            (int)(string)$response->getRdfaData()['http:content-length']->first()
        );

        $this->assertSame(
            'application/octet-stream',
            (string)$response->getRdfaData()['dc:format']->first()
        );
    }
}
