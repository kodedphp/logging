<?php

namespace Tests\Koded\Logging\PhpBench;

use Koded\Logging\Processors\ErrorLog;
use PhpBench\Attributes as Bench;

class ErrorLogBench extends AbstractBench
{
    #[Bench\Revs(1000)]
    #[Bench\Iterations(3)]
    public function benchErrorLog()
    {
        $this->log->debug(...$this->message());
        $this->log->info(...$this->message());
        $this->log->notice(...$this->message());
        $this->log->warning(...$this->message());
        $this->log->error(...$this->message());
        $this->log->critical(...$this->message());
        $this->log->alert(...$this->message());
        $this->log->emergency(...$this->message());
    }

    protected function getConfig(): array
    {
        return [
            [
                ['class' => ErrorLog::class],
            ]
        ];
    }
}
