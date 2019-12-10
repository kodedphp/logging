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

use Koded\Logging\Processors\{Cli, Processor};
use Psr\Log\LoggerTrait;
use Throwable;

/**
 * Class Log for logging different types of application messages.
 *
 * $log->notice('foo');
 * $log->warn('bar');
 *
 *  CONFIGURATION PARAMETERS (Log class)
 *
 *      -   deferred (bool)         [optional], default: false
 *          A flag to set the Log instance how to dump messages.
 *          Set to TRUE if you want to process all accumulated messages
 *          at shutdown time. Otherwise, the default behavior is to process
 *          the message immediately after the LoggerInterface method is called.
 *
 *      -   loggers (array)
 *          An array of log processors. Every processor is defined in array with it's own
 *          configuration parameters, but ALL must have the following:
 *
 *          -   class       (string)    [required]
 *              The name of the log processor class.
 *              Can create multiple same instances with different config
 *              parameters.
 *
 *          -   levels      (integer)    [optional], default: -1 (for all levels)
 *              Packed integer for bitwise comparison. See the constants in this
 *              class.
 *
 *              Example: Log::INFO | Log::ERROR | Log::ALERT
 *              Processor with these log levels will store only
 *              info, error and warning type messages.
 *
 *      -   dateformat  (string)    [optional], default: d/m/Y H:i:s.u
 *          The date format for the log message.
 *
 *      -   timezone    (string)    [optional], default: UTC
 *          The desired timezone for the DateTimeZone object.
 *
 *
 *  CONFIGURATION PARAMETERS (Processor class)
 *  Every processor may have it's own specific parameters.
 *
 */
class Log implements Logger
{
    use LoggerTrait;

    /**
     * @var bool Flag to control the messages processing
     */
    private $deferred = false;

    /**
     * @var string The date format for the message.
     */
    private $dateFormat;

    /**
     * @var string Valid timezone for the message.
     */
    private $timezone = 'UTC';

    /**
     * @var Processor[] Hash with all registered log processors.
     */
    private $processors = [];

    /**
     * @var array List with all accumulated messages.
     */
    private $messages = [];

    /**
     * Creates all requested log processors.
     *
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->deferred = (bool)($settings['deferred'] ?? false);
        $this->dateFormat = (string)($settings['dateformat'] ?? 'd/m/Y H:i:s.u');
        $this->timezone = (string)($settings['timezone'] ?? $this->timezone);

        foreach ((array)($settings['loggers'] ?? []) as $processor) {
            $this->attach(new $processor['class']($processor));
        }

        if ($this->deferred) {
            register_shutdown_function([$this, 'process']);
        }
    }

    public function attach(Processor $processor): Logger
    {
        if (0 !== $processor->levels()) {
            $this->processors[spl_object_hash($processor)] = $processor;
        }

        return $this;
    }

    public function log($level, $message, array $context = [])
    {
        try {
            $levelname = strtoupper($level);
            $level = constant('self::' . $levelname);
        } catch (Throwable $e) {
            $levelname = 'LOG';
            $level = -1;
        }

        $this->messages[] = [
            'level'     => $level,
            'levelname' => $levelname,
            'message'   => $this->formatMessage($message, $context),
            'timestamp' => date_create_immutable('now', timezone_open($this->timezone) ?: null)->format($this->dateFormat),
        ];

        $this->deferred || $this->process();
    }

    /**
     * Parses the message as in the interface specification.
     *
     * @param string|object $message A string or object that implements __toString
     * @param array         $params  [optional] Arbitrary data with key-value pairs replacements
     *
     * @return string
     */
    private function formatMessage($message, array $params = []): string
    {
        $replacements = [];
        foreach ($params as $k => $v) {
            $replacements['{' . $k . '}'] = $v;
        }

        return strtr((string)$message, $replacements);
    }

    public function process(): void
    {
        foreach ($this->processors as $processor) {
            $processor->update($this->messages);
        }

        $this->messages = [];
    }

    public function exception(Throwable $e, Processor $processor = null): void
    {
        $logger = $processor ?? new Cli([]);
        $message = $e->getMessage() . PHP_EOL . ' -- [Trace]: ' . $e->getTraceAsString();

        $this->attach($logger)->critical($message);
        $this->process();
        $this->detach($logger);
    }

    public function detach(Processor $processor): Logger
    {
        unset($this->processors[spl_object_hash($processor)]);

        return $this;
    }
}
