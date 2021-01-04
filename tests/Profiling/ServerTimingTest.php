<?php

namespace Binarcode\LaravelDeveloper\Tests\Profiling;

use Binarcode\LaravelDeveloper\Profiling\ServerTiming;
use Binarcode\LaravelDeveloper\Tests\TestCase;

class ServerTimingTest extends TestCase
{
    public function test_can_measure_timing()
    {
        /**
         * @var ServerTiming $timing
         */
        $timing = ServerTiming::startWithoutKey();

        sleep(1);

        $timing->stopAllUnfinishedEvents();

        $this->assertEquals(
            1,
            (int)$timing->getDuration()
        );
    }
}
