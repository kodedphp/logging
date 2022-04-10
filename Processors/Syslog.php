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

use Koded\Logging\Logger;
use function closelog;
use function openlog;
use function strtr;

/**
 * System log.
 *
 * @link http://www.php.net/manual/en/function.openlog.php
 */
class Syslog extends Processor
{
    protected string $format = '[levelname] message';

    protected function process(array $message): void
    {
        $levels = [
            Logger::DEBUG     => LOG_DEBUG,
            Logger::INFO      => LOG_INFO,
            Logger::NOTICE    => LOG_NOTICE,
            Logger::WARNING   => LOG_WARNING,
            Logger::ERROR     => LOG_ERR,
            Logger::CRITICAL  => LOG_CRIT,
            Logger::ALERT     => LOG_ALERT,
            Logger::EMERGENCY => LOG_EMERG
        ];

        try {
            openlog('', LOG_CONS, LOG_USER);
            \syslog($levels[$message['level']] ?? LOG_DEBUG, strtr($this->format, $message));
        } finally {
            closelog();
        }
    }
}
