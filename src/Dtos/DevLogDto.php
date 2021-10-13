<?php

namespace Binarcode\LaravelDeveloper\Dtos;

use Binarcode\LaravelDeveloper\Models\DeveloperLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DevLogDto
{
    public string $name;

    public array $payload = [];

    public ?string $tags;

    public ?Model $target = null;

    public ?Collection $relatedModels;

    public ?Collection $meta;

    public function __construct(string $name = 'Dev Log', ?array $payload = [], ?string $tags = null)
    {
        $this->payload = $payload ?? [];
        $this->name = $name;
        $this->tags = $tags;
        $this->relatedModels = collect();
        $this->meta = collect();
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

    public function target(Model $model): self
    {
        $this->target = $model;

        return $this;
    }

    public function addRelatedModel(Model $model): self
    {
        $this->relatedModels->push([
            'target_id' => $model->getKey(),
            'target_type' => $model->getMorphClass(),
        ]);

        return $this;
    }

    public function addMeta(array $meta): self
    {
        $this->meta->push($meta);

        return $this;
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
