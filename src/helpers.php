<?php

use Binarcode\LaravelDeveloper\LaravelDeveloper;
use Binarcode\LaravelDeveloper\Profiling\ServerMemory;
use Binarcode\LaravelDeveloper\Profiling\ServerTiming;

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
        $instance = new LaravelDeveloper;

        collect($args)->each(function ($item) use ($instance) {
            if (is_string($item)) {
                $instance::messageToDevSlack($item);
            }

            if ($item instanceof Throwable) {
                $instance::exceptionToDevSlack($item);
            }
        });

        return $instance;
    }
}
