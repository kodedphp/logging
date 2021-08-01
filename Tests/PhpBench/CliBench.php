<?php

namespace Tests\Koded\Logging\PhpBench;

use Koded\Logging\Processors\Cli;
use PhpBench\Attributes as Bench;

class CliBench extends AbstractBench
{
    #[Bench\Revs(1000)]
    #[Bench\Iterations(5)]
    public function benchCli()
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
                ['class' => Cli::class],
            ]
        ];
    }
}
