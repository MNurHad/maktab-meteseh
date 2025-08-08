<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EnsureAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->expectsJson()) {
            if (Auth::check()) {
                $user = Auth::user();
                $lastLogin = $user->last_login_at;

                if (!$lastLogin || Carbon::parse($lastLogin)->diffInDays(now()) >= 2) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('admin.showLogin');
                }
            } else {
                return redirect()->route('admin.showLogin');
            }
        }

        return $next($request);
    }
}

