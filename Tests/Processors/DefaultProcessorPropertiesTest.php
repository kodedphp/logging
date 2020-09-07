<?php

namespace Koded\Logging\Tests\Processors;

use Koded\Logging\Logger;
use Koded\Logging\Processors\Memory;
use Koded\Logging\Tests\LoggerAttributeTrait;
use PHPUnit\Framework\TestCase;

class DefaultProcessorPropertiesTest extends TestCase
{
    use LoggerAttributeTrait;

    public function test_defaults()
    {
        $processor = new Memory([]);

        $this->assertSame(-1, $this->property($processor, 'levels'));
        $this->assertSame('timestamp [levelname]: message', $this->property($processor, 'format'));
        $this->assertSame('', $this->property($processor, 'formatted'));
    }

    public function test_constructor_settings()
    {
        $processor = new Memory([
            'levels' => Logger::ALERT | Logger::NOTICE | Logger::WARNING,
            'format' => '[level] message'
        ]);

        $this->assertSame(50, $processor->levels());
        $this->assertSame('[level] message', $this->property($processor, 'format'));
    }
}
