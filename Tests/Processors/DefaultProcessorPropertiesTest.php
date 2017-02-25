<?php

namespace Koded\Logging\Processors;

use Koded\Logging\Logger;

class DefaultProcessorPropertiesTest extends \PHPUnit_Framework_TestCase
{

    public function testDefaults()
    {
        $processor = new Void([]);
        $this->assertAttributeSame(-1, 'levels', $processor);
        $this->assertAttributeSame('[timestamp] levelname: message', 'format', $processor);
        $this->assertAttributeSame('', 'formatted', $processor);
    }

    public function testConstructorSettings()
    {
        $processor = new Memory([
            'levels' => Logger::ALERT | Logger::NOTICE | Logger::WARNING,
            'format' => '[level] message'
        ]);

        $this->assertSame(50, $processor->levels());
        $this->assertAttributeSame('[level] message', 'format', $processor);
    }
}
