<?php

namespace Binarcode\LaravelDeveloper\Tests\Helpers;

use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Binarcode\LaravelDeveloper\Notifications\DevLog;
use Binarcode\LaravelDeveloper\Tests\TestCase;

class DevLogHelperTest extends TestCase
{
    public function test_dev_log_can_store_payload(): void
    {
        $payload = ['a' => 'b'];

        $this->assertInstanceOf(DevLog::class, $log = devLog('Dev Log', $payload));

        $log->__destruct();

        $this->assertDatabaseHas('exception_logs', [
            'name' => 'Dev Log',
        ]);

        $exceptionLog = ExceptionLog::first();

        $this->assertSame(
            $payload,
            $exceptionLog->payload
        );
    }
}
