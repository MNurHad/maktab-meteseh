<?php

namespace App\Observers;

use Spatie\Permission\Models\Role;
use App\Services\ActivityService;

class RoleObserver extends ActivityService
{
    /**
     * Handle the user "created" event.
     *
     * @param Role $role
     * @return void
     */
    public function created(Role $role)
    {
        $this->recorded($role, __FUNCTION__);
    }

    /**
     * Handle the user "updated" event.
     *
     * @param Role $role
     * @return void
     */
    public function updated(Role $role)
    {
        $this->recorded($role, __FUNCTION__);
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param Role $role
     * @return void
     */
    public function deleted(Role $role)
    {
        $this->recorded($role, __FUNCTION__);
    }
}
