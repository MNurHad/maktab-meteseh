<?php

namespace App\Observers;

use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Support\Facades\Cache;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
         $this->recorded($user, __FUNCTION__);
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        $this->recorded($user, __FUNCTION__);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        Cache::forget("banned_user:{$user->getKey()}");
        $this->recorded($user, __FUNCTION__);
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        
    }
}
