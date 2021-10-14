<?php

namespace Binarcode\LaravelDeveloper\Tests\Helpers;

use Binarcode\LaravelDeveloper\Dtos\DevLogDto;
use Binarcode\LaravelDeveloper\Models\DeveloperLog;
use Binarcode\LaravelDeveloper\Tests\TestCase;

class DevLogHelperTest extends TestCase
{
    public function test_dev_log_can_store_payload(): void
    {
        $payload = ['a' => 'b'];

        $this->assertInstanceOf(DevLogDto::class, $log = devLog('Dev Log', $payload));

        $log->__destruct();

        $this->assertDatabaseHas('developer_logs', [
            'name' => 'Dev Log',
        ]);

        $exceptionLog = DeveloperLog::first();

        $this->assertSame(
            $payload,
            $exceptionLog->payload
        );
    }

    public function test_dev_log_can_store_tags(): void
    {
        $payload = ['a' => 'b'];

        $this->assertInstanceOf(DevLogDto::class, $log = devLog('Dev Log', $payload, 'error'));

        $log->__destruct();

        $this->assertDatabaseHas('developer_logs', [
            'tags' => 'error',
        ]);

        $this->assertInstanceOf(DevLogDto::class, devLog('test'));
    }
}
