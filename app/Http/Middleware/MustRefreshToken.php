<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Auth;

class MustRefreshToken
{
    public function handle($request, Closure $next)
    {
        $guard = Auth::guard('api');
        if ($guard->user()->must_refresh_token) {
            User::where('id', $guard->id())
                ->update(['must_refresh_token' => false]);

            event(new Logout('api', $guard->user()));

            $guard->logout();

            return response()->json([
                'message' => trans('message.logout')
            ], 401);
        }

        return $next($request);
    }
}
