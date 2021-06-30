<?php

namespace Binarcode\LaravelDeveloper\Notifications;

use App\Notifications\OrderPlacedNotification;
use Binarcode\LaravelDeveloper\Dtos\DevNotificationDto;
use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Binarcode\LaravelDeveloper\Telescope\TelescopeDev;
use Binarcode\LaravelDeveloper\Telescope\TelescopeException;
use Illuminate\Notifications\Notification;
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

    protected ?string $channel = null;

    protected bool $telescope = true;

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

    public function channel(string $channel): self
    {
        $this->channel = $channel;

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
                    tap(ExceptionLog::makeFromException($item), fn(ExceptionLog $log) => $log->save())
                );

                if ($this->telescope && TelescopeDev::allow()) {
                    TelescopeException::record($item);
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

        if ($item instanceof Notification) {
            NotificationFacade::route('slack', $this->guessChannel())->notify(
                $item
            );
        }

        $notification = new $class($dto);


        if (is_callable($cb = static::$notifyUsing)) {
            return call_user_func($cb, $notification);
        }

        NotificationFacade::route('slack', $this->guessChannel())->notify(
            $notification
        );

        return $this;
    }

    public function withoutTelescope(): self
    {
        $this->telescope = false;

        return $this;
    }

    public static function notifyUsing(?callable $notificator)
    {
        Slack::$notifyUsing = $notificator;
    }

    private function guessChannel(): ?string
    {
        return $this->channel ?? config('developer.slack_dev_hook');
    }
}
