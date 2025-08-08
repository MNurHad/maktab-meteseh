<?php

namespace App\Http\Controllers\Cms;

use App\Models\Activity;
use App\Models\Otp;
use App\Http\Controllers\CmsController;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ActivityController extends CmsController
{
    /**
     * @var string
     */
    protected $resourceName = 'activities';

    /**
     * @var Activity
     */
    protected $activity;

    /**
     * ActivityController constructor.
     * @param Activity $activity
     */
    public function __construct(Activity $activity)
    {
        $this->authorizeResourceWildcard($this->resourceName);
        $this->activity = $activity;
    }

    /**
     * @return JsonResponse
     */
    protected function useDatatables(): JsonResponse
    {
        return $this->activity->getDatatables();
    }

    protected function accesDatatables(): JsonResponse
    {
        return (new Activity)->accesDatatables();
    }

    protected function otpDatatables(): JsonResponse
    {
        return (new Otp)->getDatatables();
    }

    /**
     * @return View
     */
    public function index(): View
    {
        $this->setTitle('Log Activity')
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
                ['name' => 'Log Activity', 'route' => null, 'current' => true]
            ]);

        return view("cms.{$this->resourceName}.index", $this->getData());
    }

     /**
     * @return View
     */
    public function otp(): View
    {
        $this->setTitle('Log OTP')
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
                ['name' => 'Log OTP', 'route' => null, 'current' => true]
            ]);

        return view("cms.{$this->resourceName}.log", $this->getData());
    }
}
