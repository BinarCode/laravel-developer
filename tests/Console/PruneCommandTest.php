<?php

namespace Binarcode\LaravelDeveloper\Tests\Console;

use Binarcode\LaravelDeveloper\Commands\PruneCommand;
use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Binarcode\LaravelDeveloper\Tests\TestCase;
use Laravel\Telescope\Storage\EntryModel;
use Laravel\Telescope\Tests\FeatureTestCase;

class PruneCommandTest extends TestCase
{
    public function test_prune_command_will_clear_old_records()
    {
        $recent = ExceptionLog::factory()->create(['created_at' => now()]);

        $old = ExceptionLog::factory()->create(['created_at' => now()->subDays(2)]);

        $this->artisan(PruneCommand::class)->expectsOutput('1 entries pruned.');

        $this->assertDatabaseHas('exception_logs', ['uuid' => $recent->uuid]);

        $this->assertDatabaseMissing('exception_logs', ['uuid' => $old->uuid]);
    }

    public function test_prune_command_can_vary_hours()
    {
        $recent = ExceptionLog::factory()->create(['created_at' => now()->subHours(5)]);

        $this->artisan(PruneCommand::class)->expectsOutput('0 entries pruned.');

        $this->artisan(PruneCommand::class, ['--hours' => 4])->expectsOutput('1 entries pruned.');

        $this->assertDatabaseMissing('exception_logs', ['uuid' => $recent->uuid]);
    }
}
