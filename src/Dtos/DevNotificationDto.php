<?php

namespace Binarcode\LaravelDeveloper\Dtos;

use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use JsonSerializable;
use Throwable;

/**
 * Class DevNotificationDto
 *
 * @property string message
 *
 * @property string attachment_title
 * @property string attachment_link
 * @property string attachment_content
 *
 * @package App\Models\Dto
 */
class DevNotificationDto implements JsonSerializable
{
    public $message;

    public $attachment_title;

    public $attachment_link;

    public $attachment_content;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function hasAttachment(): bool
    {
        return ! is_null($this->attachment_title) || ! is_null($this->attachment_content);
    }

    public function getAttachmentTitle(): ?string
    {
        return $this->attachment_title ?? $this->attachment_content;
    }

    public function getAttachmentContent(): ?string
    {
        return $this->attachment_content ?? $this->attachment_title;
    }

    public static function makeFromException(Throwable $t)
    {
        return new static([
            'message' => $t->getMessage(),
        ]);
    }

    public static function makeFromExceptionLog(ExceptionLog $log)
    {
        return new static([
            'message' => $log->name,
            'attachment_title' => $log->identifier,
            'attachment_link' => $log->getUrl(),
        ]);
    }

    public function jsonSerialize()
    {
        return [
            'message' => $this->message,
            'attachment_title' => $this->attachment_title,
            'attachment_content' => $this->attachment_content,
        ];
    }
}
