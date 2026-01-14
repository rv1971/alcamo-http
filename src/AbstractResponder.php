<?php

namespace alcamo\http;

use alcamo\exception\ErrorHandler;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @brief Base class for scripts that run in a web server
 *
 * Typically, the script constructs an object and calls emit(). Everything
 * else is coded in a class derived from AbstractResponder.
 *
 * @date Last reviewed 2026-01-14
 */
abstract class AbstractResponder
{
    private $errorHandler_;  ///< ErrorHandler
    private $serverRequest_; ///< ServerRequestInterface

    public function __construct(?ServerRequestInterface $serverRequest = null)
    {
        $this->errorHandler_ = new ErrorHandler();

        $this->serverRequest_ = $serverRequest
            ?? $serverRequest = ServerRequestFactory::fromGlobals();
    }

    public function getServerRequest(): ServerRequestInterface
    {
        return $this->serverRequest_;
    }

    abstract public function emit(): void;
}
