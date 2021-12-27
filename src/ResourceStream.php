<?php

namespace alcamo\http;

use alcamo\exception\Closed;
use Laminas\Diactoros\Stream;

/**
 * @brief Stream based on a resource
 */
class ResourceStream extends Stream implements EmitInterface
{
    /// Emit complete output and return number of bytes emitted
    public function emit(): ?int
    {
        if (!$this->resource) {
            /** @throw alcamo::exception::Closed if already closed. */
            throw new Closed();
        }

        if (stream_get_meta_data($this->resource)['seekable']) {
            fseek($this->resource, 0);
        }

        $count = fpassthru($this->resource);

        return $count === false ? null : $count;
    }
}
