<?php

namespace Binarcode\LaravelDeveloper\Middleware;

use Closure;
use Illuminate\Http\Request;

class DevSanctumAuthMiddleware extends DevAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $this->validate($request, $next);

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
