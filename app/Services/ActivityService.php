<?php

namespace App\Services;

use App\Models\Activity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Artisan;

abstract class ActivityService
{
    /**'
     * @param $model
     * @param string $action
     * @return void
     */
    protected function recorded($model, string $action): void
    {
        $request = request();
        $user = $request->user('api') ?? $request->user();

        if ($user) {
            $request = request();
            $detection = (new BrowserDetection($request->userAgent()))->detect();
            $body = collect($request->all())->reject(function ($value) {
                return !is_string($value);
            })->all();

            (new Activity)->forceFill([
                'ip_address' => $request->ip(),
                'browser' => $detection->getBrowser(),
                'browser_version' => $detection->getVersion(),
                'platform' => $detection->getPlatform(),
                'action' => $action,
                'additional_data' => [
                    'headers' => $request->header(),
                    'body' => $body
                ],
                'userable_id' => $user->getKey(),
                'userable_type' => get_class($user),
                'modelable_id' => $model->getKey(),
                'modelable_type' => get_class($model),
            ])->saveQuietly();
        }
    }

    /**
     * @param string $key
     * @return void
     */
    protected function forgetCacheResource(string $key): void
    {
        if (str_contains($key, 'schedules')) {
       //    Artisan::call('cache:clear');
            Cache::tags([$key])->flush();
        }
        Cache::forget($key);
    }

    /**
     * @param $user
     */
    protected function updateLastTokenLogin($user): void
    {
        if (!empty($user->last_token)) {
            if (app()->environment(['staging', 'production'])) {
                $response = $this->pushLogout($user->last_token, $user->id);
                if ($response->successful()) {
                    $jwtTTL = (int) config('jwt.ttl');
                    Cache::put("logout_user:{$user->id}", $user->last_token, $jwtTTL * 60);
                }
            }
        }

        DB::table('users')->where('id', $user->id)
            ->update(['last_token' => $user->new_token]);
    }

    /**
     * @param $token
     * @param $userId
     * @return \Illuminate\Http\Client\Response
     */
    protected function pushLogout($token, $userId)
    {
        $databaseUrl = config('services.google.database_url');
        $path = config('services.google.default_path');
        $endpoint = 'multiple_login'.'/'.$userId.'.json';
        $secret = config('services.google.default_token');

        $url = "{$databaseUrl}/{$path}/{$endpoint}?auth={$secret}";
        $data = [
            'user_id' => $userId,
            'access_token' => $token,
            'timestamp' => now()->valueOf()
        ];

        return Http::asJson()->put($url, $data);
    }
}
