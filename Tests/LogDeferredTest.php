<?php

namespace Koded\Logging;

use Koded\Logging\Processors\Memory;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class LogDeferredTest extends TestCase
{
    /**
     * @var Log
     */
    private $SUT;

    public function test_message_block()
    {
        $this->SUT->alert('Hello, {you}', ['you' => 'awesome person']);
        $message = $this->getMessages()[0];

        $this->assertSame(Log::ALERT, $message['level']);
        $this->assertSame('ALERT', $message['levelname']);
        $this->assertSame('Hello, awesome person', $message['message']);
        $this->assertSame('string', gettype($message['timestamp']));
    }

    public function test_unsupported_level_should_pass_to_default_level()
    {
        $this->SUT->log('', '');
        $message = $this->getMessages()[0];

        $this->assertSame(-1, $message['level']);
        $this->assertSame('LOG', $message['levelname']);
    }

    public function test_log_suppression()
    {
        $processor = new Memory([
            'levels' => 0 // suppress this logger completely
        ]);

        $processor->update([
            [
                'level' => -1, // this is ignored
                'levelname' => 'DEBUG',
                'message' => 'Hello',
                'timestamp' => 1234567890
            ]
        ]);

        $this->assertSame('', $processor->formatted());
    }

    protected function setUp()
    {
        $this->SUT = new Log([
            'deferred' => true,
        ]);
    }

    private function getMessages(): array
    {
        $reflected = new ReflectionProperty($this->SUT, 'messages');
        $reflected->setAccessible(true);

        return $reflected->getValue($this->SUT);
    }
}
