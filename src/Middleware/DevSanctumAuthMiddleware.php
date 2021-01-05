<?php

namespace Binarcode\LaravelDeveloper\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\HeaderBag;

class DevSanctumAuthMiddleware
{
    /**
     * @var Closure
     */
    public static $resolveUser;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (! App::environment('local')) {
            return $next($request);
        }

        if ($request->header('Authorization') !== 'Bearer testing') {
            return $next($request);
        }

        if (is_callable(static::$resolveUser)) {
            /** * @var User $user */
            $user = call_user_func(static::$resolveUser, $request);
        } else {
            /** * @var string $class */
            $class = config('app.providers.users.model');

            $user = $class::query()->first();
        }


        if (is_null($user)) {
            return $next($request);
        }

        if (! in_array(\Laravel\Sanctum\HasApiTokens::class, class_uses_recursive($user))) {
            return $next($request);
        }

        $token = $user->createToken('login')->plainTextToken;

        /** * @var HeaderBag $bag */
        $bag = $request->headers;

        $bag->add([
            'Authorization' => "Bearer {$token}",
        ]);

        return $next($request);
    }

    public static function resolveUserUsing(Closure $resolveUser): string
    {
        static::$resolveUser = $resolveUser;

        return static::class;
    }
}
