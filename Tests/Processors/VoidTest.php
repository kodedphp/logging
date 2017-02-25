<?php

namespace Koded\Logging\Processors;

class VoidTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaults()
    {
        $processor = new Void([]);

        $this->assertSame(0, $processor->levels());
        $this->assertSame('', $processor->formatted());
        $this->assertNull($processor->update([]));
    }
}
