<?php

namespace Tests\Koded\Logging;

use DateTimeZone;
use Koded\Logging\Log;
use Koded\Logging\Processors\Memory;
use PHPUnit\Framework\TestCase;

class LogConstructorTest extends TestCase
{
    use LoggerAttributeTrait;

    public function test_construction_with_config_array()
    {
        $log = new Log([
            'timezone'   => 'Europe/Berlin',
            'dateformat' => 'Y-m-d',
            'loggers'    => [
                [
                    'class'  => Memory::class,
                    'levels' => Log::ALERT | Log::ERROR,
                    'format' => '[levelname] message'
                ]
            ]
        ]);

        $this->assertSame('Y-m-d', $this->property($log, 'dateFormat'));
        $this->assertInstanceOf(DateTimeZone::class, $this->property($log, 'timezone'));
        $this->assertSame('Europe/Berlin', $this->property($log, 'timezone')->getName());
    }

    public function test_construction_without_config()
    {
        $log = new Log([]);

        $this->assertSame(false, $this->property($log, 'deferred'));
        $this->assertSame('d/m/Y H:i:s.u', $this->property($log, 'dateFormat'));
        $this->assertSame('UTC', $this->property($log, 'timezone')->getName());
    }

    public function test_invalid_timezone_setting()
    {
        $log = new Log(['timezone' => 'invalid/zone']);
        $this->assertSame('UTC', $this->property($log, 'timezone')->getName());
    }
}
