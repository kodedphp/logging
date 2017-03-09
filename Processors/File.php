<?php

namespace Koded\Logging\Processors;

use Exception;

/**
 * Log processor for storing the log messages into text files on the disk.
 *
 *  CONFIGURATION PARAMETERS
 *
 *      -   dir (string), default: sys_get_temp_dir()
 *          The directory for the log files. Must exist and be writable by the PHP.
 *
 *          NOTE:   the log filename is calculated from of the current
 *          YEAR/MONTH/DATE and appended to this directory path
 *
 *      -   extension (string), default: .log
 *          The log file extension.
 *
 */
class File extends Processor
{

    /**
     * @var string The log filename.
     */
    protected $filename = '';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->initialize($settings);
    }

    /**
     * NOTE: This method should be refactored at some point in time!
     * AVOID INIT METHODS AND CONSTRUCTORS THAT IMPLEMENTS LOGIC!
     *
     * @param array $settings
     *
     * @throws Exception
     */
    protected function initialize(array $settings)
    {
        $cwd = pathinfo($_SERVER['SCRIPT_FILENAME'] ?? '/tmp', PATHINFO_DIRNAME);
        $dir = rtrim(
                $settings['dir'] ?? $cwd . DIRECTORY_SEPARATOR . 'logs', DIRECTORY_SEPARATOR
            ) . DIRECTORY_SEPARATOR;

        if (!is_dir($dir)) {
            throw new Exception(sprintf('Log directory "%s" must exist.', $dir));
        }

        if (!is_writable($dir)) {
            throw new Exception(sprintf('Log directory "%s" must be writable.', $dir));
        }

        $dir = sprintf('%s%s%s', $dir, date('Y/m'), DIRECTORY_SEPARATOR);

        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true)) {
                // @codeCoverageIgnoreStart
                throw new Exception(sprintf('Failed to create a log directory "%s".', $dir));
                // @codeCoverageIgnoreEnd
            }
        }

        $this->filename = sprintf('%s%s%s', $dir, date('d'), $settings['extension'] ?? '.log');
    }

    protected function parse(array $message)
    {
        file_put_contents($this->filename, strtr($this->format, $message) . PHP_EOL, FILE_APPEND);
    }
}
