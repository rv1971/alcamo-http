<?php

namespace alcamo\http;

use alcamo\exception\Closed;
use Laminas\Diactoros\Stream;

/**
 * @brief Stream based on a resource
 *
 * @date Last reviewed 2026-01-14
 */
class ResourceStream extends Stream implements HavingEmitMethodInterface
{
    /**
     * @copydoc alcamo:http::HavingEmitMethodInterface::emit()
     *
     * Use fpassthru(), which may be more efficient than first getting the
     * complete stream content and then echoing it to the output.
     */
    public function emit(): ?int
    {
        if (!$this->resource) {
            /** @throw alcamo::exception::Closed if already closed. */
            throw new Closed();
        }

        if ($this->isSeekable()) {
            $this->seek(0);
        }

        $count = fpassthru($this->resource);

        return $count === false ? null : $count;
    }
}
