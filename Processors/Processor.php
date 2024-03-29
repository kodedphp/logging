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
 * Abstract class Processor for creating a concrete logger implementations.
 *
 */
abstract class Processor
{
    /**
     * @var int Packed integer for all log levels. If not specified, all levels
     *      are included (by default)
     */
    protected int $levels = -1;

    /**
     * @var string The log message format.
     */
    protected string $format = 'timestamp [levelname] message';

    /**
     * @var string Keeps all formatted log messages in this property.
     */
    protected string $formatted = '';

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->levels = (int)($settings['levels'] ?? $this->levels);
        $this->format = (string)($settings['format'] ?? $this->format);
    }

    /**
     * Receive update from the Log instance.
     * This is where the messages are filtered and processed.
     *
     * @param array{level: int, levelname: string, message: string, timestamp: string} $message
     *     The message to be processed
     *
     * @return void
     */
    public function update(array $message): void
    {
        if (($message['level'] ?? -1) & $this->levels) {
            $this->process($message);
        }
    }

    /**
     * The concrete implementation of the log processor where
     * the message is filtered and delivered.
     *
     * @param array $message
     *
     * @return void
     */
    abstract protected function process(array $message): void;

    /**
     * Returns all enabled log levels for the processor object.
     * See Logger interface constants for all available levels.
     *
     *  -1          Enable all log levels
     *   0          Disable logging, the log processor should not be loaded at all
     *   1-128      Power of 2 number, for specific log levels
     *
     * @return int A packed integer for bitwise comparison
     */
    public function levels(): int
    {
        return $this->levels;
    }

    /**
     * Returns all messages as formatted string.
     *
     * @return string
     */
    public function formatted(): string
    {
        return $this->formatted;
    }
}
