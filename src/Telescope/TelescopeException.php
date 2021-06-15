<?php

namespace Binarcode\LaravelDeveloper\Telescope;

use Illuminate\Support\Arr;
use Laravel\Telescope\ExceptionContext;
use Laravel\Telescope\IncomingExceptionEntry;
use Laravel\Telescope\Telescope;
use Throwable;

class TelescopeException
{
    public static function record(Throwable $exception, $message = null): void
    {
        $trace = collect($exception->getTrace())->map(function ($item) {
            return Arr::only($item, ['file', 'line']);
        })->toArray();

        try {
            Telescope::recordException(
                IncomingExceptionEntry::make($exception, [
                    'class' => get_class($exception),
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'message' => $message ?? $exception->getMessage(),
                    'context' => null,
                    'trace' => $trace,
                    'line_preview' => ExceptionContext::get($exception),
                ])
            );
        } catch (Throwable $e) {
        }
    }
}
