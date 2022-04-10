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

use function defined;
use function fopen;
use function fwrite;
use function strtr;

/**
 * Log processor for CLI.
 *
 */
class Cli extends Processor
{
    protected string $format = '> [timestamp][levelname] message';

    /** @var resource */
    private $handle;

    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->handle = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'w');
    }

    protected function process(array $message): void
    {
        fwrite($this->handle, strtr($this->format, $message) . PHP_EOL);
    }
}
