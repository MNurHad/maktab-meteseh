<?php

namespace App\Services;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Cache\RateLimiter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use App\Models\OsmToken;

trait Authenticate
{
    /**
     * @return View
     */
    public function showLoginForm(): View
    {
        return view('auth');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request): JsonResponse
    {
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            event(new Lockout($request));

            $seconds = $this->limiter()->availableIn(
                $this->throttleKey($request)
            );

            throw ValidationException::withMessages([
                'email' => [Lang::get('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ])],
            ])->status(Response::HTTP_TOO_MANY_REQUESTS);
        }

        if ($this->attemptLogin($request)) {
            $request->session()->regenerate();

            $this->clearLoginAttempts($request);

            $user = Auth::user();

            return response()->json([
                'statusCode' => 200,
                'message'    => 'Login Admin Successfully',
                'redirect'   => route('admin.home'),
            ], 200);
        }

        $this->incrementLoginAttempts($request);

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * @param Request $request
     * @return void
     */
    protected function validateLogin(Request $request): void
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $request->only('email', 'password'), $request->filled('remember')
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return redirect()->route('admin.showLogin');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function hasTooManyLoginAttempts(Request $request): bool
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($request), 5
        );
    }

    /**
     * Increment the login attempts for the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function incrementLoginAttempts(Request $request): void
    {
        $this->limiter()->hit(
            $this->throttleKey($request), 15 * 60
        );
    }

    /**
     * Clear the login locks for the given user credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function clearLoginAttempts(Request $request): void
    {
        $this->limiter()->clear($this->throttleKey($request));
    }

    /**
     * Get the throttle key for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input('email')).'|'.$request->ip();
    }

    /**
     * Get the rate limiter instance.
     *
     * @return \Illuminate\Cache\RateLimiter
     */
    protected function limiter()
    {
        return app(RateLimiter::class);
    }
}