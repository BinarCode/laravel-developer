<?php

namespace Binarcode\LaravelDeveloper\Commands;

use Illuminate\Console\Command;

class LaravelDeveloperCommand extends Command
{
    public $signature = 'laravel-developer';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
