<?php

namespace Binarcode\LaravelDeveloper\Commands;

use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Illuminate\Console\Command;

class PruneCommand extends Command
{
    protected $signature = 'dev:prune {--hours=24 : The number of hours to retain Developer data}';

    protected $description = 'Prune stale entries from the Developer logs.';

    public function handle()
    {
        $this->info(ExceptionLog::prune(now()->subHours($this->option('hours'))) . ' entries pruned.');
    }
}
