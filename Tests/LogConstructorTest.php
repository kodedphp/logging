<?php

namespace Koded\Logging;

use Koded\Logging\Processors\Memory;
use PHPUnit\Framework\TestCase;

class LogConstructorTest extends TestCase
{

    public function testConstructionWithConfigArray()
    {
        $log = new Log([
            'timezone' => 'Europe/Berlin',
            'dateformat' => 'Y-m-d',
            'loggers' => [
                ['class' => Memory::class, 'levels' => Log::ALERT | Log::ERROR, 'format' => '[levelname] message']
            ]
        ]);

        $this->assertAttributeSame('Y-m-d', 'dateFormat', $log);
        $this->assertAttributeSame('Europe/Berlin', 'timezone', $log);
    }

    public function testConstructionWithoutConfig()
    {
        $log = new Log([]);
        $this->assertAttributeSame('d/m/Y H:i:s', 'dateFormat', $log);
        $this->assertAttributeSame('UTC', 'timezone', $log);

        $log->register();
    }
}
