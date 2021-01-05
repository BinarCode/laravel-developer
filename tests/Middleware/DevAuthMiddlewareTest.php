<?php

namespace Binarcode\LaravelDeveloper\Tests\Middleware;

use Binarcode\LaravelDeveloper\Middleware\DevAuthMiddleware;
use Binarcode\LaravelDeveloper\Tests\Fixtures\User;
use Binarcode\LaravelDeveloper\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\HeaderBag;

class DevAuthMiddlewareTest extends TestCase
{
    public function test_can_authenticate_using_testing()
    {
        $user = new User;
        DevAuthMiddleware::resolveUserUsing(fn() => $user);

        $middleware = new DevAuthMiddleware;

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

        $this->assertAuthenticatedAs($user);
    }
}
