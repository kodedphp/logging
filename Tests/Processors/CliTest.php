<?php

namespace Koded\Logging\Tests\Processors;

use Koded\Logging\Logger;
use Koded\Logging\Processors\Cli;
use PHPUnit\Framework\TestCase;

class CliTest extends TestCase
{
    public function test_formatting()
    {
        $processor = new Cli([]);

        $processor->update([
            [
                'level' => Logger::DEBUG,
                'levelname' => 'TEST',
                'message' => 'Cli 1',
                'timestamp' => 1234567890
            ],
            [
                'level' => Logger::DEBUG,
                'levelname' => 'TEST',
                'message' => 'Cli 2',
                'timestamp' => 1234567891
            ]
        ]);

        $this->assertSame('', $processor->formatted());
    }
}
