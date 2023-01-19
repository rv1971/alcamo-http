<?php

namespace alcamo\http;

use alcamo\rdfa\{
    DcFormat,
    DcLanguage,
    DcModified,
    DcSource,
    DcTitle,
    HttpCacheControl,
    HttpContentDisposition,
    HttpContentLength,
    RdfaData
};
use PHPUnit\Framework\TestCase;

class Rdfa2HeadersTest extends TestCase
{
    /**
     * @dataProvider stmt2PairProvider
     */
    public function testStmt2Pair($stmt, $expectedPair): void
    {
        $rdfa2Headers = new Rdfa2Headers();

        $this->assertSame(
            $expectedPair,
            $rdfa2Headers->stmt2Pair($stmt)
        );
    }

    public function stmt2PairProvider(): array
    {
        return [
            'DcTitle' => [ new DcTitle('Foo'), null ],
            'DcFormat' => [
                new DcFormat('text/plain'),
                [ 'Content-Type', 'text/plain' ]
            ],
            'DcLanguage' => [
                new DcLanguage('yo'),
                [ 'Content-Language', 'yo' ]
            ],
            'DcModified' => [
                new DcModified('2023-01-19T16:08-06:00'),
                [ 'Last-Modified', 'Thu, 19 Jan 2023 16:08:00 -0600' ]
            ],
            'DcSource' => [
                new DcSource('http://www.example.biz'),
                [ 'Link', '<http://www.example.biz>; rel="canonical"' ]
            ],
            'HttpCacheControl' => [
                new HttpCacheControl('no-cache'),
                [ 'Cache-Control', 'no-cache' ]
            ],
            'HttpContentDisposition' => [
                new HttpContentDisposition('foo.json'),
                [ 'Content-Disposition', 'attachment; filename="foo.json"' ]
            ],
            'HttpContentLength' => [
                new HttpContentLength(1042),
                [ 'Content-Length', '1042' ]
            ]
        ];
    }
}
