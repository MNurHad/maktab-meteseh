<?php

namespace App\Observers;

use App\Models\EventSession;
use App\Services\ActivityService;

class SessionObserver extends ActivityService
{
    /**
     * Handle the user "created" event.
     *
     * @param  EventSession $eventSession
     * @return void
     */
    public function created(EventSession $eventSession)
    {
        $this->forgetCacheResource("sessionDetails:{$eventSession->getKey()}");
        $this->forgetCacheResource("sessions:{$eventSession->event_id}");
        $this->forgetCacheResource("schedules:{$eventSession->event_id}");
        $this->forgetCacheResource("rooms:{$eventSession->event_id}");
        $this->recorded($eventSession, __FUNCTION__);
    }

    /**
     * Handle the user "updated" event.
     *
     * @param EventSession $eventSession
     * @return void
     */
    public function updated(EventSession $eventSession)
    {
        $this->forgetCacheResource("sessionDetails:{$eventSession->getKey()}");
        $this->forgetCacheResource("sessions:{$eventSession->event_id}");
        $this->forgetCacheResource("schedules:{$eventSession->event_id}");
        $this->forgetCacheResource("rooms:{$eventSession->event_id}");
        $this->recorded($eventSession, __FUNCTION__);
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  EventSession $eventSession
     * @return void
     */
    public function deleted(EventSession $eventSession)
    {
        $this->forgetCacheResource("sessionDetails:{$eventSession->getKey()}");
        $this->forgetCacheResource("sessions:{$eventSession->event_id}");
        $this->forgetCacheResource("schedules:{$eventSession->event_id}");
        $this->forgetCacheResource("rooms:{$eventSession->event_id}");
        $this->recorded($eventSession, __FUNCTION__);
    }
}
