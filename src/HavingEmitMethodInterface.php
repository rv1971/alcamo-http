<?php

namespace alcamo\http;

/**
 * @namespace alcamo::http
 *
 * @brief Extension of some laminas classes, in particular to stream process
 * output
 */

/**
 * @brief Class having an emit() method
 *
 * @date Last reviewed 2026-01-14
 */
interface HavingEmitMethodInterface
{
    /**
     * @brief Write object content to output
     *
     * @return ?int Number of characters processed, or `null` on failure.
     */
    public function emit(): ?int;
}
