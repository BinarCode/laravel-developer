<?php

namespace Binarcode\LaravelDeveloper\Notifications;

use Binarcode\LaravelDeveloper\Dtos\DevNotificationDto;
use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Binarcode\LaravelDeveloper\Telescope\TelescopeException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Throwable;

class Slack
{
    /**
     * @var callable
     */
    public static $notifyUsing;

    protected Collection $queue;

    protected bool $persist = false;

    public function __construct($args = null)
    {
        $this->queue = collect($args)->flatten();
    }

    public static function make(...$args)
    {
        return new static($args);
    }

    public function __destruct()
    {
        $this->queue->each(function ($item) {
            $this->send($item);
        });
    }

    public function persist($persist = true): self
    {
        $this->persist = $persist;

        return $this;
    }

    private function send($item)
    {
        /**
         * @var string $class
         */
        $class = config('developer.notification', DevNotification::class);

        $dto = new DevNotificationDto;

        if ($item instanceof Throwable) {
            if ($this->persist) {
                $dto = DevNotificationDto::makeFromExceptionLog(
                    tap(ExceptionLog::makeFromException($item), fn (ExceptionLog $log) => $log->save())
                );

                if (config('developer.interacts_telescope')) {
                    TelescopeException::recordException($item);
                }
            } else {
                $dto = DevNotificationDto::makeFromException($item);
            }
        }

        if (is_string($item)) {
            $dto->setMessage($item);
        }

        if ($item instanceof ExceptionLog) {
            if ($this->persist) {
                $item->save();
            }

            $dto = $dto::makeFromExceptionLog($item);
        }

        $notification = new $class($dto);


        if (is_callable($cb = static::$notifyUsing)) {
            return call_user_func($cb, $notification);
        }

        NotificationFacade::route('slack', config('developer.slack_dev_hook'))->notify(
            $notification
        );

        return $this;
    }

    public static function notifyUsing(?callable $notificator)
    {
        Slack::$notifyUsing = $notificator;
    }
}
