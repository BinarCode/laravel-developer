<?php

namespace Binarcode\LaravelDeveloper\Dtos;

use Binarcode\LaravelDeveloper\Models\DeveloperLog;

class DevLogDto
{
    public string $name;

    public ?array $payload = [];

    public ?string $tags;

    public function __construct(string $name = 'Dev Log', ?array $payload = [], ?string $tags = null)
    {
        $this->payload = $payload;
        $this->name = $name;
        $this->tags = $tags;
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

    public function setTags(string $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public static function make(...$args): self
    {
        return new static(...$args);
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function save(): void
    {
        DeveloperLog::makeFromDevLog($this)->save();
    }

    public function __destruct()
    {
        $this->save();
    }
}
