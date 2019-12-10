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

namespace Koded\Logging;

use Koded\Logging\Processors\Processor;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Interface Logger
 *
 */
interface Logger extends LoggerInterface
{
    /*
     *
     * Log levels
     *
     */

    const EMERGENCY = 1;
    const ALERT     = 2;
    const CRITICAL  = 4;
    const ERROR     = 8;
    const WARNING   = 16;
    const NOTICE    = 32;
    const INFO      = 64;
    const DEBUG     = 128;

    /**
     * Add a log processor in the stack.
     *
     * @param Processor $processor Logger processor instance
     *
     * @return Logger
     */
    public function attach(Processor $processor): Logger;

    /**
     * Detach a log processor from registered processors.
     *
     * @param Processor $processor The log processor to detach from the stack.
     *
     * @return Logger
     */
    public function detach(Processor $processor): Logger;

    /**
     * @param Throwable $e
     * @param Processor $processor [optional]
     *
     * @return void
     */
    public function exception(Throwable $e, Processor $processor = null): void;

    /**
     * Run all log processors to save the accumulated messages
     * and clean the message stack (after the method is called,
     * the message stack is emptied).
     *
     * It may be called manually as well to instantly dump all messages
     * (i.e. for Exception handling).
     *
     * @return void
     */
    public function process(): void;
}
