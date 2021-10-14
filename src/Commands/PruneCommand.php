<?php

namespace Binarcode\LaravelDeveloper\Commands;

use Binarcode\LaravelDeveloper\Models\DeveloperLog;
use Illuminate\Console\Command;

class PruneCommand extends Command
{
    protected $signature = 'dev:prune {--hours=24 : The number of hours to retain Developer data}';

    protected $description = 'Prune stale entries from the Developer logs.';

    public function handle()
    {
        $this->info(DeveloperLog::prune(now()->subHours((int) $this->option('hours'))) . ' entries pruned.');
    }
}
