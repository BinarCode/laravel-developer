<?php

namespace Binarcode\LaravelDeveloper;

use Binarcode\LaravelDeveloper\Dtos\DevNotificationDto;
use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Binarcode\LaravelDeveloper\Notifications\DevNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Throwable;

class LaravelDeveloper
{
    /**
     * @var callable
     */
    protected static $notifyUsing;

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
        $class = config('developer.notification', DevNotification::class);

        static::notifyDev(new $class(
            DevNotificationDto::makeFromException($t)
        ));
    }

    public static function exceptionLogToDevSlack(ExceptionLog $log)
    {
        $class = config('developer.notification', DevNotification::class);

        static::notifyDev(new $class(
            DevNotificationDto::makeFromExceptionLog($log)
        ));
    }

    public function routeNotificationForSlack($notification)
    {
        return config('developer.slack_dev_hook');
    }

    public static function notifyUsing(?callable $notificator)
    {
        static::$notifyUsing = $notificator;
    }
}
