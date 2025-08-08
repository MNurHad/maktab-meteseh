<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;

class AuthenticateWithBasicAuth
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
        $credentials = $request->header('Authorization');

        if (!$credentials || !($this->authenticate($credentials))) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

    protected function authenticate($credentials)
    {
        if (!preg_match('/^Basic\s+(.*)$/i', $credentials, $matches)) {
            return false;
        }

        [$username, $password] = explode(':', base64_decode($matches[1]), 2);

        $settingUser = Setting::where('key', 'CUSTOM_API_USER')->first();
        $settingPass = Setting::where('key', 'CUSTOM_API_PASSWORD')->first();

        if (!$settingUser || !$settingPass) {
            return false;
        }

        return ($settingUser->value == $username && $settingPass->value == $password);
    }

}
