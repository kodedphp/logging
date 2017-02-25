<?php

namespace Koded\Logging\Processors;

/**
 * In-memory logger.
 *
 */
class Memory extends Processor
{

    protected function parse(array $message)
    {
        $this->formatted .= PHP_EOL . strtr($this->format, $message);
    }
}
