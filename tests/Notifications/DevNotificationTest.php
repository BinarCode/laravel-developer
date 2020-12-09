<?php

namespace Binarcode\LaravelDeveloper\Tests\Notifications;

use Binarcode\LaravelDeveloper\Models\Developer;
use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Binarcode\LaravelDeveloper\Notifications\DevNotification;
use Binarcode\LaravelDeveloper\Tests\Mock\CustomNotificationMock;
use Binarcode\LaravelDeveloper\Tests\Mock\PayloadMock;
use Binarcode\LaravelDeveloper\Tests\TestCase;
use Exception;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class DevNotificationTest extends TestCase
{
    public function test_can_notify_from_exception_log()
    {
        Notification::fake();

        ExceptionLog::makeFromException(
            new Exception('wrong'),
            $payload = new PayloadMock()
        )->notifyDevs();

        Notification::assertSentTo(new AnonymousNotifiable, DevNotification::class);
    }

    public function test_can_send_custom_notification()
    {
        Notification::fake();

        Developer::notifyUsing(function (DevNotification $argument) {
            NotificationFacade::route('slack', config('developer.slack_dev_hook'))->notify(
                new CustomNotificationMock($argument->notificationDto)
            );
        });

        ExceptionLog::makeFromException(
            new Exception('wrong'),
            $payload = new PayloadMock()
        )->notifyDevs();

        Notification::assertSentTo(new AnonymousNotifiable, CustomNotificationMock::class);

        Developer::notifyUsing(null);
    }
}
