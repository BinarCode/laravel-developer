<?php

namespace Binarcode\LaravelDeveloper\Tests\Profiling;

use Binarcode\LaravelDeveloper\Profiling\ServerMemory;
use Binarcode\LaravelDeveloper\Tests\TestCase;
use Illuminate\Support\Collection;

class ServerMemoryTest extends TestCase
{
    public function test_can_measure_memory()
    {
        /**
         * @var ServerMemory $memory
         */
        $memory = ServerMemory::measure();

        $i = Collection::times(10000, function ($i) {
            return $i;
        })->all();

        collect($i);

        $memory->stop();

        $this->assertEquals(
            1,
            round($memory->getMemory())
        );
    }
}
