<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class AdminRestriction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // WHITELIST='192.168.1.1;192.168.1.2'
        $whitelist = env('WHITELIST');

        $ipAddresses = explode(';', $whitelist);
        if (!in_array($request->ip(), $ipAddresses)) {
            return Redirect::to(env('FE_URL'));
        }

        return $next($request);
    }
}
