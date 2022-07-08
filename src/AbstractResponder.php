<?php

namespace alcamo\http;

use alcamo\collection\Collection;
use alcamo\exception\ErrorHandler;
use alcamo\sanitize\Sanitizer;
use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @brief Base class for script that run in a web server.
 *
 * Typically, the script constructs an object and calls emit(). Everything
 * else is coded in a class derived from AbstractResponder.
 */
abstract class AbstractResponder
{
    private $errorHandler_;  ///< ErrorHandler
    private $conf_;          ///< Collection
    private $serverRequest_; ///< ServerRequestInterface

    public function __construct(
        Collection $conf = null,
        ?ServerRequestInterface $serverRequest = null
    ) {
        $this->errorHandler_ = new ErrorHandler();

        $this->conf_ = $conf ?? new Collection();

        $this->serverRequest_ = $serverRequest
            ?? $serverRequest = ServerRequestFactory::fromGlobals();
    }

    public function getConf(): Collection
    {
        return $this->conf_;
    }

    public function getServerRequest(): ServerRequestInterface
    {
        return $this->serverRequest_;
    }

    public function createSanitizerFlags(): ?int
    {
        return ($this->conf_['debug'] ?? false)
            ? Sanitizer::THROW_ON_INVALID
            : null;
    }

    abstract public function emit(): void;
}
