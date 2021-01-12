<?php

namespace Binarcode\LaravelDeveloper\Tests\Models;

use Binarcode\LaravelDeveloper\Dtos\DevNotificationDto;
use Binarcode\LaravelDeveloper\LaravelDeveloper;
use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Binarcode\LaravelDeveloper\Notifications\DevNotification;
use Binarcode\LaravelDeveloper\Tests\Mock\PayloadMock;
use Binarcode\LaravelDeveloper\Tests\TestCase;
use Exception;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;

class LaravelDeveloperTest extends TestCase
{
    public function test_can_intercept_sender()
    {
        LaravelDeveloper::notifyUsing(function ($argument) {
            $this->assertInstanceOf(
                DevNotification::class,
                $argument
            );
        });

        ExceptionLog::makeFromException(
            new Exception('wrong'),
            $payload = new PayloadMock()
        )->notifyDevs();

        LaravelDeveloper::notifyUsing(null);
    }

    public function test_can_notify_any_exception()
    {
        Notification::fake();

        LaravelDeveloper::exceptionToDevSlack(
            new Exception('wew')
        );

        Notification::assertSentTo(new AnonymousNotifiable, DevNotification::class);
    }

    public function test_can_notify_any_dto()
    {
        Notification::fake();

        LaravelDeveloper::toDevSlack(
            DevNotificationDto::makeWithMessage('hey')
        );

        Notification::assertSentTo(new AnonymousNotifiable, DevNotification::class);
    }

    public function test_can_notify_with_message()
    {
        Notification::fake();

        LaravelDeveloper::messageToDevSlack('wew');

        Notification::assertSentTo(new AnonymousNotifiable, DevNotification::class);
    }
}
