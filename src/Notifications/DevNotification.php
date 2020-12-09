<?php

namespace Binarcode\LaravelDeveloper\Notifications;

use Binarcode\LaravelDeveloper\Dtos\DevNotificationDto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class DevNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public DevNotificationDto $notificationDto;

    public function __construct(DevNotificationDto $notificationDto)
    {
        $this->notificationDto = $notificationDto;
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack()
    {
        return tap(
            (
                (new SlackMessage())
                ->from(env('APP_NAME'))
                ->content($this->notificationDto->message)
            ),
            function (SlackMessage $message) {
                if ($this->notificationDto->hasAttachment()) {
                    $message->attachment(function ($att) {
                        $att->title($this->notificationDto->getAttachmentTitle(), $this->notificationDto->attachment_link)
                            ->content($this->notificationDto->getAttachmentContent());
                    });
                }
            }
        );
    }
}
