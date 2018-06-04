<?php

namespace Koded\Logging\Processors;

use Koded\Logging\Logger;
use PHPUnit\Framework\TestCase;

class SyslogTest extends TestCase
{

    public function test_formatting()
    {
        $processor = new Syslog([]);

        $processor->update([
            [
                'level' => Logger::DEBUG,
                'levelname' => 'TEST',
                'message' => 'Hello',
                'timestamp' => 1234567890
            ]
        ]);

        $this->assertSame('', $processor->formatted());
    }
}
