<?php

namespace Binarcode\LaravelDeveloper\Telescope;

use Throwable;

class TelescopeException
{
    public static function recordException(Throwable $exception, $message = null): void
    {
        if (!class_exists('Laravel\\Telescope\\Telescope') ||
            !class_exists('Laravel\\Telescope\\IncomingExceptionEntry') ||
            !class_exists('Laravel\\Telescope\\ExceptionContext')
        ) {
            return;
        }

        $trace = collect($exception->getTrace())->map(function ($item) {
            return Arr::only($item, ['file', 'line']);
        })->toArray();

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
    }
}
