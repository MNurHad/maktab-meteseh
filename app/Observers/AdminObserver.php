<?php

namespace App\Observers;

use App\Models\Admin;
use App\Services\ActivityService;

class AdminObserver extends ActivityService
{
    /**
     * Handle the user "created" event.
     *
     * @param  Admin $admin
     * @return void
     */
    public function created(Admin $admin)
    {
        $this->recorded($admin, __FUNCTION__);
    }

    /**
     * Handle the user "updated" event.
     *
     * @param Admin $admin
     * @return void
     */
    public function updated(Admin $admin)
    {
        $this->recorded($admin, __FUNCTION__);
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  Admin $admin
     * @return void
     */
    public function deleted(Admin $admin)
    {
        $this->recorded($admin, __FUNCTION__);
    }
}
