<?php

namespace alcamo\http;

use alcamo\exception\Closed;
use alcamo\process\Process;
use Laminas\Diactoros\Stream;

/**
 * @brief Stream based on stdout of a process
 *
 * @date Last reviewed 2021-06-21
 */
class PipeStream extends Stream implements EmitInterface
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

    /// Emit complete output and return number of bytes emitted
    public function emit(): ?int
    {
        if (!$this->resource) {
            /** @throw alcamo::exception::Closed if already closed. */
            throw new Closed(get_class($this));
        }

        $count = fpassthru($this->resource);

        return $count === false ? null : $count;
    }
}
