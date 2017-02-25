<?php

namespace Koded\Logging\Processors;

class VoidedTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaults()
    {
        $processor = new Voided([]);

        $this->assertSame(0, $processor->levels());
        $this->assertSame('', $processor->formatted());
        $this->assertNull($processor->update([]));
    }
}
