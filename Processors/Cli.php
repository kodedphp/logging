<?php

namespace Koded\Logging\Processors;

/**
 * Log processor for CLI apps.
 *
 */
class Cli extends Processor
{

    /**
     * @var string Message format
     */
    protected $format = '> [timestamp][levelname] - message';

    /**
     * @var resource STDOUT
     */
    private $buffer;

    public function __construct(array $settings)
    {
        parent::__construct($settings);
        defined('STDOUT') and $this->buffer = STDOUT;
    }

    public function update(array $messages)
    {
        if ($this->buffer) {
            fflush($this->buffer);
            parent::update($messages);
        }
    }

    protected function parse(array $message)
    {
        $message['levelname'] = str_pad($message['levelname'], 11, ' ', STR_PAD_BOTH);
        fwrite($this->buffer, strtr($this->format, $message) . PHP_EOL);
    }
}
