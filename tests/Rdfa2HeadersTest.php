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

    /**
     * @dataProvider createHeadersProvider
     */
    public function testCreateHeaders($inputData, $expectedHeaders): void
    {
        $rdfaData = RdfaData::newFromIterable($inputData);

        $rdfa2Headers = new Rdfa2Headers();

        $this->assertSame(
            $expectedHeaders,
            $rdfa2Headers->createHeaders($rdfaData)
        );
    }

    public function createHeadersProvider(): array
    {
        return [
            [
                [
                    'dc:format' => 'application/pdf',
                    'dc:language' => 'ln-CF',
                    'dc:modified' => '2023-01-20Z',
                    'dc:source' => 'https://www.example.org/about',
                    'dc:title' => 'About',
                    'http:cache-control' => 'public',
                    'http:content-disposition' => 'about.pdf',
                    'http:content-length' => '12345',
                ],
                [
                    'Content-Type' => [ 'application/pdf' ],
                    'Content-Language' => [ 'ln-CF' ],
                    'Last-Modified' => [ 'Fri, 20 Jan 2023 00:00:00 +0000' ],
                    'Link' => [ '<https://www.example.org/about>; rel="canonical"' ],
                    'Cache-Control' => [ 'public' ],
                    'Content-Disposition' => [ 'attachment; filename="about.pdf"' ],
                    'Content-Length' => [ '12345' ]
                ]
            ]
        ];
    }
}
