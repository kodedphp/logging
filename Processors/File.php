<?php

namespace Koded\Logging\Processors;

use Koded\Exceptions\KodedException;

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

    const E_DIRECTORY_DOES_NOT_EXIST = 1;
    const E_DIRECTORY_NOT_WRITABLE = 2;
    const E_DIRECTORY_NOT_CREATED = 3;

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
     * Prepares the directory and the log filename.
     *
     * @param array $settings
     *
     * @throws FileProcessorException
     */
    protected function initialize(array $settings)
    {
        umask(umask() | 0002);

        $cwd = pathinfo($_SERVER['SCRIPT_FILENAME'] ?? '/tmp', PATHINFO_DIRNAME);
        $dir = rtrim($settings['dir'] ?? $cwd . DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!is_dir($dir)) {
            throw new FileProcessorException(self::E_DIRECTORY_DOES_NOT_EXIST, [':dir' => $dir]);
        }

        if (!is_writable($dir)) {
            throw new FileProcessorException(self::E_DIRECTORY_NOT_WRITABLE, [':dir' => $dir]);
        }

        $dir = sprintf('%s%s%s', $dir, date('Y/m'), DIRECTORY_SEPARATOR);

        if (!is_dir($dir) && false === mkdir($dir, 0775, true)) {
            // @codeCoverageIgnoreStart
            throw new FileProcessorException(self::E_DIRECTORY_NOT_CREATED, [':dir' => $dir]);
            // @codeCoverageIgnoreEnd
        }

        $this->filename = sprintf('%s%s%s', $dir, date('d'), $settings['extension'] ?? '.log');
    }

    protected function parse(array $message)
    {
        file_put_contents($this->filename, strtr($this->format, $message) . PHP_EOL, FILE_APPEND);
    }
}

class FileProcessorException extends KodedException
{
    protected $messages = [
        File::E_DIRECTORY_DOES_NOT_EXIST => 'Log directory ":dir" must exist.',
        File::E_DIRECTORY_NOT_WRITABLE => 'Log directory ":dir" must be writable.',
        File::E_DIRECTORY_NOT_CREATED => 'Failed to create a log directory ":dir".',
    ];
}