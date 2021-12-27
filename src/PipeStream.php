<?php

namespace alcamo\http;

use alcamo\process\Process;

/**
 * @brief Stream based on stdout of a process
 *
 * @date Last reviewed 2021-06-21
 */
class PipeStream extends ResourceStream
{
    private $process_; ///< Process
    private $status_;  ///< ?int

    /// Create stream from stdout of $process
    public function __construct(Process $process)
    {
        // store Process object to prevent it from being destroyed
        $this->process_ = $process;
        parent::__construct($process->getStdout());
    }

    /// Exitcode of process, available only after close()
    public function getStatus(): ?int
    {
        return $this->status_;
    }

    public function close(): void
    {
        /** Return gracefully if already closed. */
        if (!$this->resource) {
            return;
        }

        $this->detach();
        $this->status_ = $this->process_->close();
    }
}
