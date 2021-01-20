<?php

namespace Binarcode\LaravelDeveloper;

use Binarcode\LaravelDeveloper\Dtos\DevNotificationDto;
use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Binarcode\LaravelDeveloper\Notifications\DevNotification;
use Binarcode\LaravelDeveloper\Notifications\Slack;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Throwable;

class LaravelDeveloper
{
    public static function notifyDev(Notification $notification)
    {
        if (is_callable($cb = Slack::$notifyUsing)) {
            return call_user_func($cb, $notification);
        }

        NotificationFacade::route('slack', config('developer.slack_dev_hook'))->notify(
            $notification
        );
    }

    public static function toDevSlack(DevNotificationDto $dto)
    {
        /**
         * @var string $class
         */
        $class = config('developer.notification', DevNotification::class);

        static::notifyDev(new $class(
            $dto
        ));
    }

    public static function exceptionToDevSlack(Throwable $t)
    {
        /**
         * @var string $class
         */
        $class = config('developer.notification', DevNotification::class);

        static::notifyDev(new $class(
            DevNotificationDto::makeFromException($t)
        ));
    }

    public static function messageToDevSlack(string $message)
    {
        /**
         * @var string $class
         */
        $class = config('developer.notification', DevNotification::class);

        static::notifyDev(new $class(
            DevNotificationDto::makeWithMessage($message)
        ));
    }

    public static function exceptionLogToDevSlack(ExceptionLog $log)
    {
        /**
         * @var string $class
         */
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
        Slack::$notifyUsing = $notificator;
    }
}
