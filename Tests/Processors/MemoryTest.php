<?php

namespace Tests\Koded\Logging\Processors;

use Koded\Logging\Processors\Memory;
use PHPUnit\Framework\TestCase;

class MemoryTest extends TestCase
{
    public function test_formatting()
    {
        $processor = new Memory([]);

        $processor->update(
            [
                'level'     => -1,
                'levelname' => 'TEST',
                'message'   => 'Hello',
                'timestamp' => 12345
            ]);

        $processor->update(
            [
                'level'     => -1,
                'levelname' => 'TEST',
                'message'   => 'World',
                'timestamp' => 67890
            ]);

        $this->assertStringContainsString("12345 [TEST] Hello\n67890 [TEST] World", $processor->formatted());
    }
}
