<?php

namespace Tests\Koded\Logging\PhpBench;

use Koded\Logging\Processors\Syslog;
use PhpBench\Attributes as Bench;

class SyslogBench extends AbstractBench
{
    #[Bench\Revs(1000)]
    #[Bench\Iterations(3)]
    public function benchSyslog()
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
                ['class' => Syslog::class],
            ]
        ];
    }
}
