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
 * Log processor for CLI apps.
 *
 */
class Cli extends Processor
{
    /** @var string Message format */
    protected $format = '> [timestamp][levelname] - message';

    /** @var bool */
    private $buffer;

    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->buffer = defined('STDERR');
    }

    protected function process(array $message): void
    {
        if ($this->buffer) {
            fwrite(STDERR, strtr($this->format, $message) . PHP_EOL);
        }
    }
}
