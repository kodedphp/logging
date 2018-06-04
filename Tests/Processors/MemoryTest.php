<?php

namespace Koded\Logging\Processors;

use PHPUnit\Framework\TestCase;

class MemoryTest extends TestCase
{

    public function test_formatting()
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
