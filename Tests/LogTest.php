<?php

namespace Koded\Logging;

use Exception;
use Koded\Logging\Processors\Memory;

class LogTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Log
     */
    private $SUT;

    public function testAttachAndDetach()
    {
        $processor = new Memory([]);
        $this->assertAttributeCount(0, 'processors', $this->SUT);

        $this->SUT->attach($processor);
        $this->assertAttributeCount(1, 'processors', $this->SUT);

        $this->SUT->detach($processor);
        $this->assertAttributeCount(0, 'processors', $this->SUT);
    }

    public function testMessagesStack()
    {
        $this->assertAttributeCount(0, 'messages', $this->SUT);
        $this->SUT->alert('Hello');
        $this->assertAttributeCount(1, 'messages', $this->SUT);
    }

    public function testMessageBlock()
    {
        $this->SUT->alert('Hello {you}', ['you' => 'the most awesome person in the universe']);
        $message = $this->getMessages()[0];

        $this->assertSame(Log::ALERT, $message['level']);
        $this->assertSame('ALERT', $message['levelname']);
        $this->assertSame('Hello the most awesome person in the universe', $message['message']);
        $this->assertInternalType('string', $message['timestamp']);
    }

    public function testUnsupportedLevelShouldPassToDefaultLevel()
    {
        $this->SUT->log('', '');
        $message = $this->getMessages()[0];

        $this->assertSame(-1, $message['level']);
        $this->assertSame('LOG', $message['levelname']);
    }

    public function testExceptionAttribute()
    {
        $processor = new Memory([]);
        $this->SUT->exception(new Exception('The message', 1), $processor);
        $this->assertAttributeContains('ALERT: The message', 'formatted', $processor);
    }

    public function testLogSuppression()
    {
        $processor = new Memory([
            'levels' => 0 // suppress the logger completely
        ]);

        $processor->update([
            [
                'level' => -1, // this is ignored
                'levelname' => 'TEST',
                'message' => 'Hello',
                'timestamp' => 1234567890
            ]
        ]);

        $this->assertSame('', $processor->formatted());
    }

    public function testRegister()
    {
        $mock = $this
            ->getMockBuilder(Log::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertNull($mock->register());
    }

    protected function setUp()
    {
        $this->SUT = new Log([]);
    }

    private function getMessages(): array
    {
        $reflected = new \ReflectionProperty($this->SUT, 'messages');
        $reflected->setAccessible(true);
        return $reflected->getValue($this->SUT);
    }
}
