<?php

namespace Binarcode\LaravelDeveloper\Tests\Notifications;

use Binarcode\LaravelDeveloper\LaravelDeveloper;
use Binarcode\LaravelDeveloper\Models\DeveloperLog;
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
    public function test_can_notify_from_exception_log(): void
    {
        Notification::fake();

        DeveloperLog::makeFromException(
            new Exception('wrong'),
            $payload = new PayloadMock()
        )->notifyDevs();

        Notification::assertSentTo(new AnonymousNotifiable(), DevNotification::class);
    }

    public function test_can_relate_target(): void
    {
        Notification::fake();
        $log = DeveloperLog::factory()->create();

        devLog('Targetable')
            ->target($log)
            ->addRelatedModel($log)
            ->addMeta(['browser' => 'safari',])
            ->save();

        $this->assertDatabaseHas('developer_logs', [
            'name' => 'Targetable',
            'target_id' => '1',
        ]);
    }

    public function test_can_send_custom_notification(): void
    {
        Notification::fake();

        LaravelDeveloper::notifyUsing(function (DevNotification $argument) {
            NotificationFacade::route('slack', config('developer.slack_dev_hook'))->notify(
                new CustomNotificationMock($argument->notificationDto)
            );
        });

        DeveloperLog::makeFromException(
            new Exception('wrong'),
            $payload = new PayloadMock()
        )->notifyDevs();

        Notification::assertSentTo(new AnonymousNotifiable(), CustomNotificationMock::class);

        LaravelDeveloper::notifyUsing(null);
    }

    public function test_can_set_custom_config_notification(): void
    {
        Notification::fake();

        config([
            'developer.notification' => CustomNotificationMock::class,
        ]);

        DeveloperLog::makeFromException(
            new Exception('wrong'),
            $payload = new PayloadMock()
        )->notifyDevs();

        Notification::assertSentTo(new AnonymousNotifiable(), CustomNotificationMock::class);
    }
}
