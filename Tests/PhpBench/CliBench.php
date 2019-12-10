<?php

namespace Koded\Logging\Tests\PhpBench;

use Koded\Logging\Processors\Cli;

class CliBench extends AbstractBench
{
    /**
     * @Revs(1000)
     * @Iterations(3)
     */
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
            'deferred' => false,
            'loggers'  => [
                ['class' => Cli::class],
            ]
        ];
    }
}
