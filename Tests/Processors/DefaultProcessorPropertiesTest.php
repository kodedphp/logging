<?php

namespace Koded\Logging\Processors;

use Koded\Logging\Logger;
use PHPUnit\Framework\TestCase;

class DefaultProcessorPropertiesTest extends TestCase
{

    public function test_defaults()
    {
        $processor = new Voided([]);
        $this->assertAttributeSame(-1, 'levels', $processor);
        $this->assertAttributeSame('[timestamp] levelname: message', 'format', $processor);
        $this->assertAttributeSame('', 'formatted', $processor);
    }

    public function test_constructor_settings()
    {
        $processor = new Memory([
            'levels' => Logger::ALERT | Logger::NOTICE | Logger::WARNING,
            'format' => '[level] message'
        ]);

        $this->assertSame(50, $processor->levels());
        $this->assertAttributeSame('[level] message', 'format', $processor);
    }
}
