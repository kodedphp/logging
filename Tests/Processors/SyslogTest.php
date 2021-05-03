<?php

namespace Tests\Koded\Logging\Processors;

use Koded\Logging\Logger;
use Koded\Logging\Processors\Syslog;
use PHPUnit\Framework\TestCase;

class SyslogTest extends TestCase
{
    public function test_formatting()
    {
        $processor = new Syslog([]);

        $processor->update([
            [
                'level' => Logger::DEBUG,
                'message' => 'Syslog 1',
                'timestamp' => 1234567890
            ],
            [
                'level' => Logger::DEBUG,
                'message' => 'Syslog 2',
                'timestamp' => 1234567891
            ]
        ]);

        $this->assertSame('', $processor->formatted());
    }
}
