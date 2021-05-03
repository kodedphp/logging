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

use Exception;
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
 *          YEAR/MONTH/DATE and appended to this directory path (ex: /path/to/logs/2000/01/01.log)
 *
 *      -   extension (string), default: .log
 *          The log file extension.
 */
class File extends Processor
{
    private string $dir = '';
    private string $ext = '';

    /**
     * {@inheritdoc}
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);

        \umask(\umask() | 0002);
        $this->ext = (string)($settings['extension'] ?? '.log');
        $this->dir = \rtrim((string)$settings['dir'], '/');

        if (false === \is_dir($this->dir)) {
            throw FileProcessorException::directoryDoesNotExist($this->dir);
        }

        if (false === \is_writable($this->dir)) {
            throw FileProcessorException::directoryIsNotWritable($this->dir);
        }

        $this->dir .= '/';
    }

    protected function process(array $message): void
    {
        try {
            // The filename should be calculated at the moment of writing
            $dir = $this->dir . \date('Y/m');
            \is_dir($dir) || \mkdir($dir, 0775, true);

            \file_put_contents($dir . '/' . \date('d') . $this->ext, \strtr($this->format, $message) . PHP_EOL,
                FILE_APPEND);

            // @codeCoverageIgnoreStart
        } catch (Exception $e) {
            \error_log($e->getMessage());
            // @codeCoverageIgnoreEnd
        }
    }
}


class FileProcessorException extends KodedException
{
    private const
        E_DIRECTORY_DOES_NOT_EXIST = 1,
        E_DIRECTORY_NOT_WRITABLE = 2;

    protected array $messages = [
        self::E_DIRECTORY_DOES_NOT_EXIST => 'Log directory ":dir" must exist',
        self::E_DIRECTORY_NOT_WRITABLE   => 'Log directory ":dir" must be writable',
    ];

    public static function directoryDoesNotExist(string $directory): static
    {
        return new static(static::E_DIRECTORY_DOES_NOT_EXIST, [':dir' => $directory]);
    }

    public static function directoryIsNotWritable(string $directory): static
    {
        return new static(static::E_DIRECTORY_NOT_WRITABLE, [':dir' => $directory]);
    }
}
