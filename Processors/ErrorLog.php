<?php

/*
 * This file is part of the Koded package.
 *
 * (c) Mihail Binev <mihail@kodeart.com>
 *
 * Please view the LICENSE distributed with this source code
 * for the full copyright and license information.
 *
 */

namespace Koded\Logging\Processors;

/**
 * ErrorLog sends the log message to PHP system logger.
 *
 */
class ErrorLog extends Processor
{
    protected function parse(array $message): void
    {
        error_log(strtr($this->format, $message), 0);
    }
}
