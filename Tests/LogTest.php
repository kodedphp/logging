<?php

namespace Tests\Koded\Logging;

use Exception;
use Koded\Logging\Log;
use Koded\Logging\Processors\Memory;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    use LoggerAttributeTrait;

    public function test_default_setup()
    {
        $log = new Log;

        $this->assertSame('d/m/Y H:i:s.u', $this->property($log, 'dateformat'));
        $this->assertSame('UTC', $this->property($log, 'timezone')->getName());
        $this->assertEmpty($this->property($log, 'processors'));
    }

    public function test_attach_and_detach()
    {
        $log = new Log;
        $processor = new Memory([]);

        $this->assertCount(0, $this->property($log, 'processors'));

        $log->attach($processor);
        $this->assertCount(1, $this->property($log, 'processors'));

        $log->detach($processor);
        $this->assertCount(0, $this->property($log, 'processors'));
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
        $log = new Log;

        $processor = new Memory([]);
        $log->exception(new Exception('The error message', 1), $processor);

        $this->assertStringContainsString('[CRITICAL]', $this->property($processor, 'formatted'));
        $this->assertStringContainsString('The error message', $this->property($processor, 'formatted'));
    }

    public function test_log_level()
    {
        $log = new Log;
        $processor = new Memory([]);
        $log->attach($processor);

        $log->log('TESTING', 'Hello {u}', ['u' => 'Universe']);

        $this->assertStringContainsString(
            '[LOG] Hello Universe',
            $processor->formatted(),
            'The unknown level is defaulted to "LOG"'
        );
    }
}
