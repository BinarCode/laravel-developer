<?php

namespace Binarcode\LaravelDeveloper\Models;

use Binarcode\LaravelDeveloper\Dtos\DevLogDto;
use Binarcode\LaravelDeveloper\Enums\TagEnum;
use Binarcode\LaravelDeveloper\LaravelDeveloper;
use Binarcode\LaravelDeveloper\Models\Concerns\WithCreator;
use Binarcode\LaravelDeveloper\Models\Concerns\WithUuid;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use JsonSerializable;
use Throwable;

/**
 * Class ExceptionLog
 * @property string $id
 * @property string $uuid
 * @property-read string $identifier
 * @property string $name
 * @property string $file
 * @property string $line
 * @property string $code
 * @property string $tags
 * @property array $exception
 * @property array $previous
 * @property array $meta
 * @property array $related_models
 * @property string $target_type
 * @property id $target_id
 * @property array|mixed $payload
 * @property Carbon $created_at
 * @package App\Models
 */
class DeveloperLog extends Model
{
    use HasFactory;
    use WithUuid;
    use WithCreator;

    protected $table = 'developer_logs';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'exception' => 'array',
        'related_models' => 'array',
        'meta' => 'array',
    ];

    public function getTable(): string
    {
        return config('developer.table') ?? $this->table;
    }

    public static function makeFromDevLog(DevLogDto $log): self
    {
        return new static([
            'uuid' => (string) Str::uuid(),
            'name' => $log->name,
            'payload' => $log->payload,
            'tags' => $log->tags,
            'target_id' => $log->target ? $log->target->getKey() : null,
            'target_type' => $log->target ? $log->target->getMorphClass() : null,
            'related_models' => $log->relatedModels->toArray(),
            'meta' => $log->meta->toArray(),
        ]);
    }

    public static function makeFromException(Throwable $throwable, JsonSerializable $payload = null): self
    {
        return new static([
            'uuid' => (string) Str::uuid(),
            'name' => Str::substr($throwable->getMessage(), 0, 255),
            'file' => $throwable->getFile(),
            'line' => $throwable->getLine(),
            'code' => $throwable->getCode(),
            'exception' => $throwable->__toString(),
            'previous' => (string) $throwable->getPrevious(),
            'payload' => optional($payload)->jsonSerialize(),
            'tags' => TagEnum::danger,
        ]);
    }

    public function addPayload(JsonSerializable $payload): self
    {
        if (is_null($this->payload)) {
            $this->payload = [$payload->jsonSerialize()];

            return $this;
        }

        $this->payload = collect($this->payload)->push($payload->jsonSerialize());

        return $this;
    }

    public function getIdentifierAttribute()
    {
        return "Exception Log: [{$this->uuid}].";
    }

    public function notifyDevs(): self
    {
        $this->save();

        LaravelDeveloper::exceptionLogToDevSlack($this);

        return $this;
    }

    public function getUrl(): ?string
    {
        if (! config('developer.developer_log_base_url')) {
            return null;
        }

        return Str::replaceArray(
            '{id}',
            ['{id}' => $this->id,],
            config('developer.developer_log_base_url')
        );
    }

    public static function prune(DateTimeInterface $before)
    {
        $query = static::query()->where('created_at', '<', $before);

        $totalDeleted = 0;

        do {
            $deleted = $query->take(1000)->delete();

            $totalDeleted += $deleted;
        } while ($deleted !== 0);

        return $totalDeleted;
    }
}
