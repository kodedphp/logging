<?php

namespace Koded\Logging\Processors;

/**
 * System log.
 *
 * @link http://www.php.net/manual/en/function.openlog.php
 */
class Syslog extends Processor
{

    /**
     * The syslog ident string added to each message.
     */
    const IDENT = 'KODED';

    protected $format = '[levelname] message';

    public function update(array $messages)
    {
        if (count($messages)) {
            openlog(self::IDENT, LOG_PID | LOG_CONS, LOG_USER);
            parent::update($messages);
            closelog();
        }
    }

    protected function parse(array $message)
    {
        syslog($message['level'], strtr($this->format, $message));
    }
}
