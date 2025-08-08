<?php

namespace App\Services;

use App\Notifications\ResetPassword as ResetPasswordNotification;
use App\Models\Log;
use App\Events\LogProcessed;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

trait ResetPassword
{
    /**
     * @param Request $request
     * @param null $token
     * @return View
     */
    public function showResetForm(Request $request, $token = null): View
    {
        return view('cms.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * @return View
     */
    public function showLinkRequestForm(): View
    {
        return view('cms.forgot-password');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => [
                'required',
                'email',
                "exists:{$this->model()},email"
            ]
        ]);

        $response = $this->broker()->sendResetLink(
            $request->only('email'),
            function ($user, $token) {
                $user->notify(new ResetPasswordNotification($token));
                event(new LogProcessed([
                    'action' => 'SEND_RESET_LINK',
                    'identifier' => $token,
                    'email' => $user->email,
                    'status' => 'SUCCESS'
                ], Log::TYPE_EMAIL));
            }
        );

        if ($response == Password::RESET_LINK_SENT) {
            return response()->json([
                'message' => trans($response)
            ]);
        }

        throw ValidationException::withMessages([
            'email' => [trans($response)]
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function reset(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => [
                'required',
                'string',
                'max:50',
                'email',
                "exists:{$this->model()},email"
            ],
            'password' => 'required|confirmed|min:6',
        ]);

        $response = $this->broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();
            }
        );

        if ($response == Password::PASSWORD_RESET) {
            return response()->json(['message' => trans($response)]);
        }

        throw ValidationException::withMessages([
            'email' => [trans($response)]
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    protected function broker()
    {
        return Password::broker();
    }

    /**
     * @return string
     */
    protected function model()
    {
        return 'admins';
    }
}