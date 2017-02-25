<?php

namespace Koded\Logging\Processors;

/**
 * ErrorLog sends the log message to PHP's system logger.
 *
 */
class ErrorLog extends Processor
{

    protected function parse(array $message)
    {
        error_log(strtr($this->format, $message), 0);
    }
}