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
 * @date Last reviewed 2021-06-17
 */
interface EmitInterface
{
    /**
     * @brief Write object content to output
     *
     * @return ?int Number of characters processed, or `null` on failure.
     */
    public function emit(): ?int;
}
