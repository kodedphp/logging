<?php

namespace Koded\Logging;

use DateTime;
use DateTimeZone;
use Koded\Logging\Processors\{ ErrorLog, Processor };
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
 *      -   loggers (array)
 *          An array of log processors. Every processor is defined in array with it's own
 *          configuration parameters, but ALL must have the following:
 *
 *      -   class       (string)    [required]
 *          The name of the log processor class.
 *          Can create multiple same instances with different config
 *          parameters.
 *
 *      -   levels      (integer)    [optional], default: -1 (for all levels)
 *          Packed integer for bitwise comparison. See the constants in this
 *          class.
 *
 *          Example: Log::INFO | Log::ERROR | Log::ALERT
 *          Processor with these log levels will store only
 *          info, error and warning type messages.
 *
 *      -   dateformat  (string)    [optional], default: d/m/Y H:i:s
 *          The date format for the log message.
 *
 *      -   timezone    (string)    [optional], default: UTC
 *          The desired timezone for the DateTimeZone object.
 *
 *
 *  CONFIGURATION PARAMETERS (Processor class)
 *  Every processor has it's own specific parameters (with the above directives).
 *
 */
class Log implements Logger
{

    use LoggerTrait;

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
        $this->dateFormat = $settings['dateformat'] ?? 'd/m/Y H:i:s';
        $this->timezone = $settings['timezone'] ?? $this->timezone;

        // Build and attach all requested processors
        foreach ($settings['loggers'] ?? [] as $processor) {
            $this->attach(new $processor['class']($processor));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        register_shutdown_function([$this, 'process']);
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = [])
    {
        try {
            $levelname = strtoupper($level);
            $level = constant('self::' . $levelname);
        } catch (Throwable $e) {
            $levelname = 'LOG';
            $level = -1;
        }

        $microtime = microtime(true);

        $this->messages[] = [
            'level' => $level,
            'levelname' => $levelname,
            'message' => $this->formatMessage($message, $context),
            'timestamp' => (
                (new DateTime(null, new DateTimeZone('UTC')))
                    ->setTimestamp($microtime)
                    ->format($this->dateFormat)
                ) . substr(sprintf('%.6F', $microtime), -7)
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function exception(Throwable $e, Processor $processor = null)
    {
        $syslog = $processor ?? new ErrorLog([]);
        $message = $e->getMessage() . PHP_EOL . ' -- [Trace]: ' . $e->getTraceAsString();

        $this->attach($syslog);
        $this->alert($message);
        $this->process();
        $this->detach($syslog);
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {
        foreach ($this->processors as $processor) {
            $processor->update($this->messages);
        }

        $this->messages = [];
    }

    /**
     * Add a log processor in the stack.
     *
     * @param Processor $processor Logger processor instance
     *
     * @return Log
     */
    public function attach(Processor $processor): Log
    {
        if (0 !== $processor->levels()) {
            $this->processors[spl_object_hash($processor)] = $processor;
        }

        return $this;
    }

    /**
     * Detach a log processor from registered processors.
     *
     * @param Processor $processor The log processor to detach from the stack.
     *
     * @return Log
     */
    public function detach(Processor $processor): Log
    {
        unset($this->processors[spl_object_hash($processor)]);

        return $this;
    }

    /**
     * Parses the message as in the interface specification.
     *
     * @param string|object $message A string or object that implements __toString
     * @param array $context [optional] Arbitrary data with key-value pairs replacements
     *
     * @return string
     */
    private function formatMessage($message, array $context = []): string
    {
        $replacements = [];
        foreach ($context as $k => $v) {
            $replacements['{' . $k . '}'] = $v;
        }

        return strtr((string)$message, $replacements);
    }
}
