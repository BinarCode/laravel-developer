<?php

namespace Binarcode\LaravelDeveloper\Tests\Mock;

use Binarcode\LaravelDeveloper\Notifications\DevNotification;

class CustomNotificationMock extends DevNotification
{
    public function toSlack()
    {
        return parent::toSlack();
    }
}
