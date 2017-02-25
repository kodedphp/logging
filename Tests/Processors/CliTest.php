<?php

namespace Koded\Logging\Processors;

use Koded\Logging\Logger;

class CliTest extends \PHPUnit_Framework_TestCase
{

    public function testFormatting()
    {
        $processor = new Cli([]);

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
