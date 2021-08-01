<?php

namespace Tests\Koded\Logging\PhpBench;

use Koded\Logging\Processors\File;
use PhpBench\Attributes as Bench;

class FileBench extends AbstractBench
{
    #[Bench\Revs(1000)]
    #[Bench\Iterations(3)]
    public function benchFile()
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
                ['class' => File::class, 'dir' => sys_get_temp_dir()],
            ]
        ];
    }
}
