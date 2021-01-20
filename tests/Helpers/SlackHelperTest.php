<?php

namespace Binarcode\LaravelDeveloper\Tests\Helpers;

use Binarcode\LaravelDeveloper\LaravelDeveloper;
use Binarcode\LaravelDeveloper\Notifications\DevNotification;
use Binarcode\LaravelDeveloper\Tests\TestCase;
use Exception;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

class SlackHelperTest extends TestCase
{
    public function test_slack_helper_returns_laravel_developer_instance()
    {
        $this->assertInstanceOf(LaravelDeveloper::class, slack());
        $this->assertInstanceOf(LaravelDeveloper::class, slack('message'));
        $this->assertInstanceOf(LaravelDeveloper::class, slack(new Exception()));
    }

    public function test_slack_helper_can_send_message_to_slack()
    {
        Notification::fake();

        $this->assertInstanceOf(LaravelDeveloper::class, slack('message'));

        Notification::assertSentTo(new AnonymousNotifiable, DevNotification::class);
    }

    public function test_slack_helper_can_send_throwable_to_slack()
    {
        Notification::fake();

        $this->assertInstanceOf(LaravelDeveloper::class, slack(new Exception()));

        Notification::assertSentTo(new AnonymousNotifiable, DevNotification::class);
    }
}
