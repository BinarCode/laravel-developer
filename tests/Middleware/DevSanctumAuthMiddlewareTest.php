<?php

namespace Binarcode\LaravelDeveloper\Tests\Middleware;

use Binarcode\LaravelDeveloper\Middleware\DevSanctumAuthMiddleware;
use Binarcode\LaravelDeveloper\Tests\Fixtures\User;
use Binarcode\LaravelDeveloper\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\HeaderBag;

class DevSanctumAuthMiddlewareTest extends TestCase
{
    public function test_can_authenticate_and_change_bearer_using_testing()
    {
        DevSanctumAuthMiddleware::resolveUserUsing(fn () => new User());

        $middleware = new DevSanctumAuthMiddleware();

        $request = tap(new Request(), function (Request $request) {
            /** * @var HeaderBag $bag */
            $bag = $request->headers;

            $bag->add([
                'Authorization' => "Bearer testing",
            ]);
        });

        $next = function ($request) {
            return $request;
        };

        App::partialMock()
            ->expects('environment')
            ->andReturnTrue();

        $middleware->handle($request, $next);

        $this->assertTrue($request->hasHeader('Authorization'));
        $this->assertSame($request->header('Authorization'), 'Bearer foo');
    }
}
