<?php

namespace Koded\Logging\Processors;

/**
 * Voided processor, does nothing and it's completely useless.
 *
 */
final class Voided extends Processor
{

    public function __construct(array $settings)
    {
    }

    public function update(array $messages)
    {
        $this->parse($messages);
    }

    public function levels(): int
    {
        return 0;
    }

    protected function parse(array $message)
    {
    }
}
