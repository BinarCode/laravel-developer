<?php

use Binarcode\LaravelDeveloper\Dtos\DevLogDto;
use Binarcode\LaravelDeveloper\Notifications\Slack;
use Binarcode\LaravelDeveloper\Profiling\ServerMemory;
use Binarcode\LaravelDeveloper\Profiling\ServerTiming;
use Binarcode\LaravelDeveloper\Telescope\TelescopeException;

if (! function_exists('measure_memory')) {
    function measure_memory($callable = null, string $key = 'action', string $unit = 'mb')
    {
        /** @var ServerMemory $timing */
        $timing = app(ServerMemory::class);

        if (is_callable($callable)) {
            $timing->setMemory($key, $callable);

            return dd($timing->getMemory($key, $unit));
        }

        $timing->measure($key);

        return dd($timing->getMemory($key, $unit));
    }
}

if (! function_exists('measure_timing')) {
    function measure_timing($callable = null, string $key = 'action')
    {
        /** @var ServerTiming $timing */
        $timing = app(ServerTiming::class);

        if (is_callable($callable)) {
            $timing->setDuration($key, $callable);

            return dd($timing->getDuration($key));
        }

        $timing->measure($key);

        return dd($timing->getDuration($key));
    }
}

if (! function_exists('slack')) {
    function slack(...$args)
    {
        return Slack::make($args);
    }
}

if (! function_exists('devLog')) {
    function devLog(...$args): DevLogDto
    {
        return DevLogDto::make(...$args);
    }
}

if (! function_exists('telescopeException')) {
    function telescopeException(Throwable $exception, $message = null): void
    {
        if (config('developer.interacts_telescope')) {
            TelescopeException::record($exception, $message);
        }
    }
}
