<?php

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

    /**
     * Log levels
     */
    const EMERGENCY = 1;
    const ALERT = 2;
    const CRITICAL = 4;
    const ERROR = 8;
    const WARNING = 16;
    const NOTICE = 32;
    const INFO = 64;
    const DEBUG = 128;

    /**
     * @param Throwable $e
     * @param Processor $processor [optional]
     *
     * @return void
     */
    public function exception(Throwable $e, Processor $processor = null);

    /**
     * Run all log processors to save the accumulated messages
     * and clean the message stack (after the method is called,
     * the message stack is emptied).
     *
     * This method should be registered somewhere in the bootstrap phase with
     * $log::register() method.
     *
     * It may be called manually as well to instantly dump all messages
     * (i.e. for Exception handling).
     *
     * @return void
     */
    public function process();

    /**
     * Registers the process method at the PHP's shutdown phase.
     *
     * @return void
     */
    public function register();
}
