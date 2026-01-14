<?php

namespace alcamo\http;

use Laminas\HttpHandlerRunner\Emitter\SapiEmitter as BaseSapiEmitter;
use Psr\Http\Message\ResponseInterface;

/**
 * @brief Enhanced emitter
 *
 * If the body implements HavingEmitMethodInterface, use the body's emit()
 * method instead of echoing the body's string representation. PipeStream
 * exploits this to use the fpassthru() php function for emission.
 *
 * Otherwise, create a `Content-Length` header and echo the body's string
 * representation.
 *
 * @date Last reviewed 2026-01-14
 */
class SapiEmitter extends BaseSapiEmitter
{
    private function emitBody(ResponseInterface $response): void
    {
        if ($response->getBody() instanceof HavingEmitMethodInterface) {
            $response->getBody()->emit();
        } else {
            $body = (string)$response->getBody();

            header('Content-Length: ' . strlen($body));

            echo $body;
        }
    }
}
