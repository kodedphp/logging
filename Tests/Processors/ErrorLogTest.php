<?php

namespace Koded\Logging\Processors;

use Koded\Logging\Logger;
use PHPUnit\Framework\TestCase;

class ErrorLogTest extends TestCase
{

    public function test_formatting()
    {
        $processor = new ErrorLog([]);

        $processor->update([
            [
                'level' => Logger::DEBUG,
                'levelname' => 'INFO',
                'message' => 'ErrorLog 1',
                'timestamp' => 1234567890
            ],
            [
                'level' => Logger::DEBUG,
                'levelname' => 'WARN',
                'message' => 'ErrorLog 2',
                'timestamp' => 1234567891
            ]
        ]);

        $this->assertSame('', $processor->formatted());
    }
}
