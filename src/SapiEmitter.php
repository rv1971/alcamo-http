<?php

namespace alcamo\http;

use Laminas\HttpHandlerRunner\Emitter\{EmitterInterface, SapiEmitterTrait};
use Psr\Http\Message\ResponseInterface;

/**
 * @brief Enhanced emitter
 *
 * Largely inspired by Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
 * enhancements include:
 * - A Content-Length header can be generated.
 * - If the body implements EmitInterface, the body's emit() method is used
 *   instead of echoing the body's string representation. PipeStream exploits
 *   this to use the fpassthru() php function for emission.
 *
 * @date Last reviewed 2021-06-17
 */
class SapiEmitter implements EmitterInterface
{
    use SapiEmitterTrait;

    /**
     * @brief Emit the message body.
     *
     * @param $response Message containing the body to emit.
     *
     * @param $sendContentLength Whether to emit a Content-Length header.
     *
     * If the body implements EmitInterface, the body's emit() method is used
     * instead of echoing the body's string representation.
     */
    public function emit(
        ResponseInterface $response,
        ?bool $sendContentLength = null
    ): bool {
        $this->assertNoPreviousOutput();

        $this->emitHeaders($response);

        $this->emitStatusLine($response);

        if ($sendContentLength) {
            $body = (string)$response->getBody();

            header('Content-Length: ' . strlen($body));

            echo $body;
        } else {
            if ($response->getBody() instanceof EmitInterface) {
                $response->getBody()->emit();
            } else {
                echo $response->getBody();
            }
        }

        return true;
    }
}
