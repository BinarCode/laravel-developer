<?php

namespace Binarcode\LaravelDeveloper\Tests\Helpers;

use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Binarcode\LaravelDeveloper\Notifications\DevNotification;
use Binarcode\LaravelDeveloper\Notifications\Slack;
use Binarcode\LaravelDeveloper\Tests\Fixtures\DummyNotification;
use Binarcode\LaravelDeveloper\Tests\TestCase;
use Exception;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\Notification as NotificationBase;

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
            'developer.exception_log_base_url' => 'app.test/{id}',
        ]);

        $this->assertInstanceOf(Slack::class, slack(new Exception('not found', 404))->persist());

        $this->assertDatabaseCount('exception_logs', 1);

        $this->assertDatabaseHas('exception_logs', [
            'tags' => 'danger',
        ]);

        $uuid = ExceptionLog::latest()->first()->id;

        Notification::assertSentTo(new AnonymousNotifiable, DevNotification::class,
            function (DevNotification $class) use ($uuid) {
                return $class->notificationDto->attachment_link === "app.test/{$uuid}";
            });
    }

    public function test_slack_helper_can_send_notifications_to_slack()
    {
        Notification::fake();

        config([
            'developer.exception_log_base_url' => 'app.test/{id}',
            'developer.slack_dev_hook' => 'https://test.com',
        ]);

        $this->assertInstanceOf(Slack::class, slack(
            new DummyNotification()
        )->persist());

        Notification::assertSentTo(new AnonymousNotifiable, DummyNotification::class);
    }
}
