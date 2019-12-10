<?php

namespace Koded\Logging;

use Exception;
use Koded\Logging\Processors\Memory;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    /**
     * @var Log
     */
    private $SUT;

    public function test_default_setup()
    {
        $this->assertAttributeSame(false, 'deferred', $this->SUT);
        $this->assertAttributeSame('d/m/Y H:i:s.u', 'dateFormat', $this->SUT);
        $this->assertAttributeSame('UTC', 'timezone', $this->SUT);
        $this->assertAttributeEmpty('processors', $this->SUT);
        $this->assertAttributeEmpty('messages', $this->SUT);
    }

    public function test_attach_and_detach()
    {
        $processor = new Memory([]);
        $this->assertAttributeCount(0, 'processors', $this->SUT);

        $this->SUT->attach($processor);
        $this->assertAttributeCount(1, 'processors', $this->SUT);

        $this->SUT->detach($processor);
        $this->assertAttributeCount(0, 'processors', $this->SUT);
    }

    public function test_log_suppression()
    {
        $processor = new Memory([
            'levels' => 0 // suppress the logger completely
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

    public function test_exception()
    {
        $processor = new Memory([]);
        $this->SUT->exception(new Exception('The message', 1), $processor);

         $this->assertAttributeContains('[CRITICAL]', 'formatted', $processor);
         $this->assertAttributeContains('The message', 'formatted', $processor);
    }

    protected function setUp(): void
    {
        $this->SUT = new Log([]);
    }
}
