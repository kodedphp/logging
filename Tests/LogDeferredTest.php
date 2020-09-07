<?php

namespace Koded\Logging\Tests;

use Koded\Logging\Log;
use Koded\Logging\Processors\Memory;
use PHPUnit\Framework\TestCase;

class LogDeferredTest extends TestCase
{
    use LoggerAttributeTrait;

    private $log;

    public function test_message_block()
    {
        $this->log->alert('Hello, {you}', ['you' => 'awesome person']);
        $message = $this->property($this->log, 'messages')[0];

        $this->assertSame(Log::ALERT, $message['level']);
        $this->assertSame('ALERT', $message['levelname']);
        $this->assertSame('Hello, awesome person', $message['message']);
        $this->assertSame('string', gettype($message['timestamp']));
    }

    public function test_unsupported_level_should_pass_to_default_level()
    {
        $this->log->log('', '');
        $message = $this->property($this->log, 'messages')[0];

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
                'level'     => -1, // this is ignored
                'levelname' => 'DEBUG',
                'message'   => 'Hello',
                'timestamp' => 1234567890
            ]
        ]);

        $this->assertSame('', $processor->formatted());
    }

    protected function setUp(): void
    {
        $this->log = new Log([
            'deferred' => true,
        ]);
    }
}
