<?php

namespace Binarcode\LaravelDeveloper\Notifications;

use Binarcode\LaravelDeveloper\Models\ExceptionLog;

class DevLog
{
    public array $payload;

    public string $name;

    public function __construct(string $name = 'Dev Log', array $payload = [])
    {
        $this->payload = $payload;
        $this->name = $name;
    }

    public function setPayload(array $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public static function make(string $name = 'Dev Log', array $payload = null): self
    {
        return new static($name, $payload);
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function __destruct()
    {
        ExceptionLog::makeFromDevLog($this)->save();
    }

}
