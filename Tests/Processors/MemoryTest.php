<?php

namespace Koded\Logging\Processors;

use function Koded\Stdlib\dump;
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
            ],
            [
                'level' => -1,
                'levelname' => 'TEST',
                'message' => 'World',
                'timestamp' => 1234567891
            ]
        ]);

        $this->assertContains("1234567890 [TEST]: Hello\n1234567891 [TEST]: World", $processor->formatted());
    }
}
