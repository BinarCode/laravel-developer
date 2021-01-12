<?php

namespace Binarcode\LaravelDeveloper\Middleware;

use Binarcode\LaravelDeveloper\Contracts\Sanctumable;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class DevAuthMiddleware
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
        $user = $this->validate($request, $next);

        Auth::setUser($user);

        return $next($request);
    }

    public static function resolveUserUsing(Closure $resolveUser): string
    {
        static::$resolveUser = $resolveUser;

        return static::class;
    }

    protected function validate(Request $request, Closure $next): ?Sanctumable
    {
        if (! App::environment('local')) {
            return $next($request);
        }

        if ($request->header('Authorization') !== 'Bearer '. config('developer.auth.bearer', 'testing')) {
            return $next($request);
        }

        if (is_callable(static::$resolveUser)) {
            $user = call_user_func(static::$resolveUser, $request);
        } else {
            /** * @var string $class */
            $class = config('app.providers.users.model');

            $user = $class::query()->first();
        }

        if (! ($user instanceof Authenticatable)) {
            return $next($request);
        }

        return $user;
    }
}
