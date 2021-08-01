<?php declare(strict_types=1);

namespace Tests\Koded\Logging;

use DateTimeZone;
use Koded\Logging\Log;
use Koded\Logging\Processors\Cli;
use Koded\Logging\Processors\Memory;
use PHPUnit\Framework\TestCase;

class LogConstructorTest extends TestCase
{
    use LoggerAttributeTrait;

    public function test_construction_with_config_array()
    {
        $config = [
            [
                [
                    'class' => Memory::class,
                    'levels' => Log::ALERT | Log::ERROR,
                    'format' => '[levelname] message'
                ]
            ],
            'dateformat' => 'Y-m-d',
            'timezone' => 'Europe/Berlin'
        ];

        $log = new Log(...$config);

        $this->assertSame('Y-m-d', $this->property($log, 'dateformat'));
        $this->assertInstanceOf(DateTimeZone::class, $this->property($log, 'timezone'));
        $this->assertSame('Europe/Berlin', $this->property($log, 'timezone')->getName());
    }

    public function test_construction_without_config()
    {
        $log = new Log([]);

        $this->assertSame('d/m/Y H:i:s.u', $this->property($log, 'dateformat'));
        $this->assertSame('UTC', $this->property($log, 'timezone')->getName());
    }

    public function test_invalid_timezone_setting()
    {
        $log = new Log([['class' => Cli::class]], timezone: 'invalid/zone');
        $this->assertSame('UTC', $this->property($log, 'timezone')->getName());
    }
}
