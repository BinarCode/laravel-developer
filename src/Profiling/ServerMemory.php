<?php

namespace Binarcode\LaravelDeveloper\Profiling;

class ServerMemory
{
    /** @var array */
    protected $finishedEvents = [];

    /** @var array */
    protected $startedEvents = [];

    public function start(string $key = 'measure')
    {
        $this->startedEvents[$key] = memory_get_usage();

        return $this;
    }

    public function stop(string $key = 'measure')
    {
        if (! array_key_exists($key, $this->startedEvents)) {
            return $this;
        }

        $this->setMemory(
            $key,
            memory_get_usage() - $this->startedEvents[$key]
        );

        unset($this->startedEvents[$key]);

        return $this;
    }

    public function setMemory(string $key, $duration)
    {
        if (is_callable($duration)) {
            $this->start($key);

            call_user_func($duration);

            $this->stop($key);
        } else {
            $this->finishedEvents[$key] = $duration;
        }

        return $this;
    }

    public function getMemory(string $key = 'measure', $unit = 'mb')
    {
        return $this->finishedEvents[$key]
            ? collect([

                'byte' => fn ($v) => $v,
                'mb' => fn ($v) => $v / (1000 * 1000),
                'gb' => fn ($v) => $v / (1000 * 1000 * 1000),

            ])->get($unit, 'mb')($this->finishedEvents[$key])
            : null;
    }

    public static function measure(string $key = 'measure'): ServerMemory
    {
        return app(static::class)->start($key);
    }
}
