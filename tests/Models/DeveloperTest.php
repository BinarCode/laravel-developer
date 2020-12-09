<?php

namespace Binarcode\LaravelDeveloper\Tests\Models;

use Binarcode\LaravelDeveloper\Models\Developer;
use Binarcode\LaravelDeveloper\Models\ExceptionLog;
use Binarcode\LaravelDeveloper\Notifications\DevNotification;
use Binarcode\LaravelDeveloper\Tests\Mock\PayloadMock;
use Binarcode\LaravelDeveloper\Tests\TestCase;
use Exception;

class DeveloperTest extends TestCase
{
    public function test_can_intercept_sender()
    {
        Developer::notifyUsing(function($argument) {
            $this->assertInstanceOf(
                DevNotification::class,
                $argument
            );
        });

        ExceptionLog::makeFromException(
            new Exception('wrong'),
            $payload = new PayloadMock()
        )->notifyDevs();

        Developer::notifyUsing(null);
    }
}
