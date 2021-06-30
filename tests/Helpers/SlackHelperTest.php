<?php

namespace Binarcode\LaravelDeveloper\Tests\Helpers;

use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Binarcode\LaravelDeveloper\Notifications\DevNotification;
use Binarcode\LaravelDeveloper\Notifications\Slack;
use Binarcode\LaravelDeveloper\Tests\TestCase;
use Exception;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

class SlackHelperTest extends TestCase
{
    public function test_slack_helper_returns_laravel_developer_instance()
    {
        Notification::fake();

        $this->assertInstanceOf(Slack::class, slack());
        $this->assertInstanceOf(Slack::class, slack('message'));
        $this->assertInstanceOf(Slack::class, slack(new Exception()));
    }

    public function test_slack_helper_can_send_message_to_slack()
    {
        Notification::fake();

        $this->assertInstanceOf(Slack::class, slack('message'));

        Notification::assertSentTo(new AnonymousNotifiable, DevNotification::class);
    }

    public function test_slack_helper_can_send_throwable_to_slack()
    {
        Notification::fake();

        config([
            'developer.exception_log_base_url' => 'app.test/{uuid}',
        ]);

        $this->assertInstanceOf(Slack::class, slack(new Exception('not found', 404))->persist());

        $this->assertDatabaseCount('exception_logs', 1);

        $this->assertDatabaseHas('exception_logs', [
            'tags' => 'danger',
        ]);

        $uuid = ExceptionLog::latest()->first()->uuid;

        Notification::assertSentTo(new AnonymousNotifiable, DevNotification::class, function (DevNotification $class) use ($uuid) {
            return $class->notificationDto->attachment_link === "app.test/{$uuid}";
        });
    }
}
