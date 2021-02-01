<?php

namespace Binarcode\LaravelDeveloper\Middleware;

use Binarcode\LaravelDeveloper\Tests\Fixtures\User;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class DevSanctumAuthMiddleware extends DevAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('OPTIONS')) {
            return $next($request);
        }

        if (! App::environment('local')) {
            return $next($request);
        }

        /**
         * @var User $user
         */
        $user = $this->validate($request, $next);

        if (! $user instanceof Authenticatable) {
            return $next($request);
        }

        if (! in_array(\Laravel\Sanctum\HasApiTokens::class, class_uses_recursive($user))) {
            return $next($request);
        }

        $token = $user->createToken('login')->plainTextToken;

        $bag = $request->headers;

        $bag->add([
            'Authorization' => "Bearer {$token}",
        ]);

        return $next($request);
    }
}
