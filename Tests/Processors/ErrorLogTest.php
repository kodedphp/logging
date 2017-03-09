<?php

namespace Koded\Logging\Processors;

use Koded\Logging\Logger;
use PHPUnit\Framework\TestCase;

class ErrorLogTest extends TestCase
{

    public function testFormatting()
    {
        $processor = new ErrorLog([]);

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
