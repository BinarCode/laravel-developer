<?php

namespace Binarcode\LaravelDeveloper\Models;

use Binarcode\LaravelDeveloper\LaravelDeveloper;
use Binarcode\LaravelDeveloper\Models\Concerns\WithCreator;
use Binarcode\LaravelDeveloper\Models\Concerns\WithUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use JsonSerializable;
use Throwable;

/**
 * Class ExceptionLog
 * @property string $uuid
 * @property-read string $identifier
 * @property string $name
 * @property array $exception
 * @property array|mixed $payload
 * @package App\Models
 */
class ExceptionLog extends Model
{
    use HasFactory;
    use WithUuid;
    use WithCreator;

    protected $table = 'exception_logs';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'exception' => 'array',
    ];

    public function getKeyName()
    {
        return 'uuid';
    }

    public static function makeFromException(Throwable $throwable, JsonSerializable $payload = null): self
    {
        return new static([
            'name' => Str::substr($throwable->getMessage(), 0, 255),
            'exception' => $throwable->__toString(),
            'payload' => optional($payload)->jsonSerialize(),
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
        if (! config('developer.exception_log_base_url')) {
            return null;
        }

        return Str::replaceArray('{uuid}', ['{uuid}' => $this->uuid,], config('developer.exception_log_base_url'));
    }
}
