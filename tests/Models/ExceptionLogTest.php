<?php

namespace Binarcode\LaravelDeveloper\Tests\Models;

use Binarcode\LaravelDeveloper\Models\DeveloperLog;
use Binarcode\LaravelDeveloper\Tests\Mock\PayloadMock;
use Binarcode\LaravelDeveloper\Tests\TestCase;
use Exception;

class ExceptionLogTest extends TestCase
{
    public function test_can_create_from_exception()
    {
        DeveloperLog::makeFromException(
            new Exception('wrong')
        )->save();

        $this->assertDatabaseHas('developer_logs', [
            'name' => 'wrong',
        ]);
    }

    public function test_can_serialize_payload()
    {
        DeveloperLog::makeFromException(
            new Exception('wrong'),
            $payload = new PayloadMock()
        )->save();

        $this->assertDatabaseHas('developer_logs', [
            'name' => 'wrong',
            'payload' => '{"message":"wew"}',
        ]);
    }

    public function test_can_serialize_multiple_payloads()
    {
        DeveloperLog::makeFromException(
            new Exception('wrong'),
        )
            ->addPayload(
                $payload = new PayloadMock()
            )
            ->addPayload(
                new PayloadMock()
            )
            ->save();

        $this->assertDatabaseHas('developer_logs', [
            'name' => 'wrong',
            'payload' => json_encode([
                $payload->jsonSerialize(),
                $payload->jsonSerialize(),
            ]),
        ]);
    }

    public function test_can_generate_link()
    {
        config([
            'developer.exception_log_base_url' => 'foo/{id}',
        ]);

        $log = tap(DeveloperLog::makeFromException(
            new Exception('wrong'),
            $payload = new PayloadMock()
        ), function (DeveloperLog $log) {
            $log->save();
        });

        $this->assertSame(
            'foo/' . $log->id,
            $log->getUrl()
        );
    }
}
