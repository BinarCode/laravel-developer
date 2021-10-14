<?php

namespace Binarcode\LaravelDeveloper\Tests\Fixtures;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class DummyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack()
    {
        return (new SlackMessage())
            ->from('test')
            ->content("test")
            ->attachment(function ($att) {
                $att->title("Title")->content("Content");
            });
    }
}
