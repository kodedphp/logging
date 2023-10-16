<?php declare(strict_types=1);

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
use DateTimeZone;
use Psr\Log\LoggerTrait;
use Stringable;
use Throwable;
use function constant;
use function date_create_immutable;
use function spl_object_id;
use function strtoupper;
use function strtr;
use function timezone_open;

/**
 * Class Log for logging different types of application messages.
 *
 * $log->notice('foo');
 * $log->warn('bar');
 *
 *  CONFIGURATION PARAMETERS (Log class)
 *
 *      -   processors (array)
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
 *  Every processor may have its own specific parameters.
 *
 */
class Log implements Logger
{
    use LoggerTrait;

    private const DATE_FORMAT = 'd/m/Y H:i:s.u';
    private DateTimeZone $timezone;

    /**
     * Creates all requested log processors.
     *
     * @param array<int, Processor>  $processors a list of log processors
     * @param string $dateformat The date format for the messages
     * @param string $timezone   The timezone for the messages
     */
    public function __construct(
        private array $processors = [],
        private string $dateformat = self::DATE_FORMAT,
        string $timezone = 'UTC')
    {
        $this->timezone = @timezone_open($timezone) ?: timezone_open('UTC');
        foreach ($processors as $i => $processor) {
            unset($this->processors[$i]);
            $this->attach(new $processor['class']($processor));
        }
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        try {
            $levelName = strtoupper($level);
            $level = constant('static::' . $levelName);
        } catch (Throwable) {
            $levelName = 'LOG';
            $level = -1;
        }
        foreach ($this->processors as $processor) {
            $processor->update([
               'level' => $level,
               'levelname' => $levelName,
               'message' => $this->formatMessage($message, $context),
               'timestamp' => $this->now(),
           ]);
        }
    }

    public function attach(Processor $processor): Logger
    {
        if (0 !== $processor->levels()) {
            $this->processors[spl_object_id($processor)] = $processor;
        }
        return $this;
    }

    public function detach(Processor $processor): Logger
    {
        unset($this->processors[spl_object_id($processor)]);
        return $this;
    }

    public function exception(Throwable $e, Processor $processor = null): void
    {
        $this->attach($processor ??= new Cli([]));
        $this->critical($e->getMessage() . PHP_EOL . ' [Trace]: ' . $e->getTraceAsString());
        $this->detach($processor);
    }

    /**
     * Parses the message as in the interface specification.
     *
     * @param object|string $message A string or object that implements __toString
     * @param array         $params  [optional] Arbitrary data with key-value pairs replacements
     *
     * @return string
     */
    private function formatMessage(object|string $message, array $params = []): string
    {
        $replacements = [];
        foreach ($params as $k => $v) {
            $replacements['{' . $k . '}'] = $v;
        }
        return strtr((string)$message, $replacements);
    }

    private function now(): string
    {
        return date_create_immutable('now', $this->timezone)
            ->format($this->dateformat);
    }
}
