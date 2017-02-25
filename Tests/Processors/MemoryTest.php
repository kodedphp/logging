<?php

namespace Koded\Logging\Processors;

class MemoryTest extends \PHPUnit_Framework_TestCase
{

    public function testFormatting()
    {
        $processor = new Memory([]);

        $processor->update([
            [
                'level' => -1,
                'levelname' => 'TEST',
                'message' => 'Hello',
                'timestamp' => 1234567890
            ]
        ]);

        $this->assertContains('[1234567890] TEST: Hello', $processor->formatted());
    }
}
