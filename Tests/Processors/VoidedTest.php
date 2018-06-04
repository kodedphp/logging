<?php

namespace Koded\Logging\Processors;

use PHPUnit\Framework\TestCase;

class VoidedTest extends TestCase
{

    public function test_defaults()
    {
        $processor = new Voided([]);

        $this->assertSame(0, $processor->levels());
        $this->assertSame('', $processor->formatted());
        $this->assertNull($processor->update([]));
    }
}
