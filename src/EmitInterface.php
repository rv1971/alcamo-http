<?php

namespace alcamo\http;

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
