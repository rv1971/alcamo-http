<?php

namespace alcamo\http;

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
        $serverRequest = ServerRequestFactory::fromGlobals(
            [ 'server' => 1 ],
            [ 'get' => 2 ],
            [ 'post' => 3 ],
            [ 'cookie' => 4 ],
            []
        );

        $responder1 = new MyResponder($serverRequest);

        $this->assertSame($serverRequest, $responder1->getServerRequest());

        $this->expectException(\ErrorException::class);

        /* This triggers an error because E_WARNING is not allowed in
         * trigger_error(). */
        trigger_error('Lorem ipsum', E_WARNING);
    }
}
