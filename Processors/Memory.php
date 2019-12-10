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
 * In-memory logger.
 *
 */
class Memory extends Processor
{
    protected function parse(array $message): void
    {
        $this->formatted .= PHP_EOL . strtr($this->format, $message);
    }
}
