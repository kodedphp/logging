<?php

namespace Koded\Logging\Tests;

use Exception;
use Koded\Logging\Log;
use Koded\Logging\Processors\Memory;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    use LoggerAttributeTrait;

    private $log;

    public function test_default_setup()
    {
        $this->assertSame(false, $this->property($this->log, 'deferred'));
        $this->assertSame('d/m/Y H:i:s.u', $this->property($this->log, 'dateFormat'));
        $this->assertSame('UTC', $this->property($this->log, 'timezone')->getName());

        $this->assertEmpty($this->property($this->log, 'processors'));
        $this->assertEmpty($this->property($this->log, 'messages'));
    }

    public function test_attach_and_detach()
    {
        $processor = new Memory([]);
        $this->assertCount(0, $this->property($this->log, 'processors'));

        $this->log->attach($processor);
        $this->assertCount(1, $this->property($this->log, 'processors'));

        $this->log->detach($processor);
        $this->assertCount(0, $this->property($this->log, 'processors'));
    }

    public function test_log_suppression()
    {
        $processor = new Memory([
            'levels' => 0 // suppress the logger completely
        ]);

        $processor->update([
            [
                'level'     => -1, // this message is ignored
                'levelname' => 'DEBUG',
                'message'   => 'Hello',
                'timestamp' => 1234567890
            ]
        ]);

        $this->assertSame('', $processor->formatted());
    }

    public function test_exception()
    {
        $processor = new Memory([]);
        $this->log->exception(new Exception('The message', 1), $processor);

        $this->assertContains('[CRITICAL]', $this->property($processor, 'formatted'));
        $this->assertContains('The message', $this->property($processor, 'formatted'));
    }

    protected function setUp(): void
    {
        $this->log = new Log([]);
    }
}
