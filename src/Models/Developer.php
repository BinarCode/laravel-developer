<?php

namespace Binarcode\LaravelDeveloper\Models;

use Binarcode\LaravelDeveloper\Dtos\DevNotificationDto;
use Binarcode\LaravelDeveloper\Notifications\DevNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class Developer
{
    /**
     * @var callable
     */
    public static $notifyUsing;

    public static function notifyDev(Notification $notification)
    {
        if (is_callable($cb = static::$notifyUsing)) {
            return call_user_func($cb, $notification);
        }

        NotificationFacade::route('slack', config('developer.slack_dev_hook'))->notify(
            $notification
        );
    }

    public static function exceptionToDevSlack(Throwable $t)
    {
        static::notifyDev(new DevNotification(
            DevNotificationDto::makeFromException($t)
        ));
    }

    public static function exceptionLogToDevSlack(ExceptionLog $log)
    {
        static::notifyDev(new DevNotification(
            DevNotificationDto::makeFromExceptionLog($log)
        ));
    }

    public function routeNotificationForSlack($notification)
    {
        return config('developer.slack_dev_hook');
    }

    public static function notifyUsing(callable $notificator)
    {
        static::$notifyUsing = $notificator;
    }
}


