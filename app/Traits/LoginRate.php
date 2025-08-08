<?php

namespace App\Traits;

use Illuminate\Support\Facades\RateLimiter;

trait LoginRate
{
    protected function hasTooManyLoginAttempts($request)
    {
        return RateLimiter::tooManyAttempts($this->throttleKey($request), $this->maxAttempts());
    }

    protected function incrementLoginAttempts($request)
    {
        RateLimiter::hit($this->throttleKey($request), $this->decayMinutes() * 60);
    }

    protected function clearLoginAttempts($request)
    {
        RateLimiter::clear($this->throttleKey($request));
    }

    protected function throttleKey($request)
    {
        return strtolower($request->input($this->username())) . '|' . $request->ip();
    }

    protected function maxAttempts()
    {
        return 5;
    }

    protected function decayMinutes()
    {
        return 1;
    }
}