<?php

namespace Binarcode\LaravelDeveloper\Tests\Models;


use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Binarcode\LaravelDeveloper\Tests\Mock\PayloadMock;
use Binarcode\LaravelDeveloper\Tests\TestCase;
use Exception;

class ExceptionLogTest extends TestCase
{
    public function test_can_create_from_exception()
    {
        ExceptionLog::makeFromException(
            new Exception('wrong')
        )->save();

        $this->assertDatabaseHas('exception_logs', [
            'name' => 'wrong',
        ]);
    }

    public function test_can_serialize_payload()
    {
        ExceptionLog::makeFromException(
            new Exception('wrong'),
            $payload = new PayloadMock()
        )->save();

        $this->assertDatabaseHas('exception_logs', [
            'name' => 'wrong',
            'payload' => '{"message":"wew"}'
        ]);
    }

    public function test_can_generate_link()
    {
        config([
            'developer.exception_log_base_url' => 'foo/{uuid}',
        ]);

        $log = tap(ExceptionLog::makeFromException(
            new Exception('wrong'),
            $payload = new PayloadMock()
        ), function(ExceptionLog $log) {
            $log->save();
        });

        $this->assertSame(
            'foo/' . $log->uuid,
            $log->getUrl()
        );
    }
}
