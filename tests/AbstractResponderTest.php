<?php

namespace alcamo\http;

use alcamo\collection\Collection;
use alcamo\sanitize\Sanitizer;
use Laminas\Diactoros\ServerRequestFactory;
use PHPUnit\Framework\TestCase;

class MyResponder extends AbstractResponder
{
    public function emit(): void
    {
    }
}

class AbstractResponderTest extends TestCase
{
    public function testBasics()
    {
        $conf = new Collection(
            [
                'foo' => 42,
                'bar' => 'Lorem ipsum',
                'debug' => true
            ]
        );

        $serverRequest = ServerRequestFactory::fromGlobals(
            [ 'server' => 1 ],
            [ 'get' => 2 ],
            [ 'post' => 3 ],
            [ 'cookie' => 4 ],
            []
        );

        $responder1 = new MyResponder($conf, $serverRequest);

        $this->assertSame($conf, $responder1->getConf());

        $this->assertSame($serverRequest, $responder1->getServerRequest());

        $this->assertSame(
            Sanitizer::THROW_ON_INVALID,
            $responder1->createSanitizerFlags()
        );

        $responder2 = new MyResponder(null, $serverRequest);

        $this->assertNull($responder2->createSanitizerFlags());

        $this->expectException(\ErrorException::class);

        /* This triggers an error because E_WARNING is not allowed in
         * trigger_error(). */
        trigger_error('Lorem ipsum', E_WARNING);
    }
}
