<?php

namespace alcamo\http;

use alcamo\iana\MediaType;
use alcamo\rdfa\RdfaData;
use PHPUnit\Framework\TestCase;

class FileResponseTest extends TestCase
{
    public function testNewFromPath()
    {
        $response = FileResponse::newFromPath(__FILE__);

        $this->assertSame(
            file_get_contents(__FILE__),
            (string)$response->getBody()
        );

        $this->assertSame(
            filesize(__FILE__),
            (int)(string)$response->getRdfaData()['header:content-length']
        );

        $this->assertSame(
            (string)MediaType::newFromFilename(__FILE__),
            (string)$response->getRdfaData()['dc:format']
        );

        $response = FileResponse::newFromPath(
            __FILE__,
            [ 'dc:format' => 'application/octet-stream' ]
        );

        $this->assertSame(
            file_get_contents(__FILE__),
            (string)$response->getBody()
        );

        $this->assertSame(
            filesize(__FILE__),
            (int)(string)$response->getRdfaData()['header:content-length']
        );

        $this->assertSame(
            'application/octet-stream',
            (string)$response->getRdfaData()['dc:format']
        );
    }
}
