<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\User;
use App\Observers\AdminObserver;
use App\Observers\CategoryObserver;
use App\Observers\RoleObserver;
use App\Observers\SessionObserver;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Collection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment(['production', 'staging'])) {
            URL::forceScheme('https');
        }

        Validator::extend('hash', function ($attribute, $value, $parameters, $validator) {
            return Hash::check($value, $parameters[0]);
        });

        Collection::macro('randomColor', function () {
            return $this->map(function () {
                $part = function () {
                    return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
                };
                return '#' . $part() . $part() . $part();
            });
        });
    }
}
