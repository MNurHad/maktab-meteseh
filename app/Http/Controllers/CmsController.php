<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Activity;
use App\Models\Maktab;
use App\Models\Group;
use App\Services\AuthorizesRequests;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class CmsController extends Controller
{
     use AuthorizesRequests, ValidatesRequests;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param string $key
     * @param $value
     * @return $this
     */
    protected function setData(string $key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    protected function setTitle(string $value)
    {
        return $this->setData('title', $value);
    }

    /**
     * @param array $values
     * @return $this
     */
    protected function setBreadcrumbs(array $values)
    {
        return $this->setData('breadcrumbs', $values);
    }

    /**
     * @return array
     */
    protected function getData(): array
    {
        if (property_exists($this, 'resourceName')) {
            $this->setData('resourceName', $this->resourceName);
        }

        if (property_exists($this, 'subResourceName')) {
            $this->setData('subResourceName', $this->subResourceName);
        }

        if (property_exists($this, 'organizationType')) {
            $this->setData('organizationType', $this->organizationType);
        }

        return $this->data;
    }

    /**
     * @return JsonResponse
     */
    public function datatables(): JsonResponse
    {
        if (method_exists($this, 'useDatatables')) {
            return $this->useDatatables();
        }

        return abort(404);
    }

    /**
     * @return string
     */
    protected function viewDirectory(): string
    {
        return 'cms';
    }

    /**
     * @return string
     */
    protected function guardName(): string
    {
        return 'web';
    }

    /**
     * @return string
     */
    protected function routeGroupName(): string
    {
        return 'admin';
    }

    /**
     * @param Request $request
     * @return void
     */
    protected function handlePeriod(Request $request): void
    {
        if (!$request->filled('period')) {
            $request->merge([
                'started_at' => null,
                'ended_at' => null
            ]);

            return;
        }

        $period = explode(' - ', $request->period);
        list($startDate, $endDate) = $period;

        $request->merge([
            'started_at' => Carbon::parse($startDate)->toDateTimeString(),
            'ended_at' => Carbon::parse($endDate)->toDateTimeString()
        ]);
    }

    /**
     * @param $userableId
     * @param $userableType
     * @param $column
     * @return array
     */
    protected function chartDonutActivity($userableId, $userableType, $column): array
    {
        $activities = DB::table('activities')
            ->selectRaw("{$column}, COUNT(id) AS total")
            ->where([
                ['userable_id', '=', $userableId],
                ['userable_type', '=', $userableType],
            ])
            ->groupBy($column)
            ->get()
            ->map(function ($item) use ($column) {
                return [$item->$column, $item->total];
            });


        $colors = $activities->randomColor();

        return [$activities->all(), $colors->all()];
    }

    /**
     * @return View
     */
    public function home(): View
    {
        $currentDate = Carbon::now();

        $this->setTitle('Home')
            ->setBreadcrumbs([
                ['name' => 'Home', 'route' => 'admin.home', 'current' => true],
                ['name' => Auth::user()->name, 'route' => null, 'current' => true],
            ]);

        return view("{$this->viewDirectory()}.dashboard", $this->getData());
    }

    public function setDataHome(): JsonResponse
    {
        $counts = DB::table('maktabs')
                ->selectRaw('
                    COUNT(CASE WHEN is_available = 1 THEN 1 END) AS available,
                    COUNT(CASE WHEN is_available = 0 THEN 1 END) AS not_available
                ')
                ->first();

        $assigns = DB::table('assign_maktabs')
        ->select('group_data', 'vehicle_data')
        ->get();

        $totalJamaah = 0;
        $vehicleCounts = [];

        foreach ($assigns as $row) {
            $group = json_decode($row->group_data, true) ?? [];
            $vehicle = json_decode($row->vehicle_data, true) ?? [];

            $jamaah = isset($group['jamaah']) ? (int) $group['jamaah'] : 0;
            $totalJamaah += $jamaah;

            if (!empty($vehicle['vehicle'])) {
                $vehicleName = $vehicle['vehicle'];
                if (!isset($vehicleCounts[$vehicleName])) {
                    $vehicleCounts[$vehicleName] = 0;
                }
                $vehicleCounts[$vehicleName] += 1;
            }
        }

        return response()->json([
            'statusCode' => 200,
            'data' => [
                'published' => $counts->available,
                'recommended' => $counts->not_available,
                'jamaah' => $totalJamaah,
                'vehicles' => $vehicleCounts
            ]
        ]);
    }

    public function getSessionAccess()
    {
        $data = DB::table('activities')
        ->select('platform', DB::raw('COUNT(*) as jml'))
        ->groupBy('platform')
        ->get();
    
        $platforms = $data->pluck('platform')->toArray();
        $counts = $data->pluck('jml')->toArray();
        
        return response()->json([
            'statusCode' => 200,
            'data' => [
                'platform'    => $platforms,
                'jmlPlatform' => $counts
            ]
        ], 200);
    }

    public function getSessionBrowser()
    {
        $data = DB::table('activities')
        ->select('browser', DB::raw('COUNT(*) as jml'))
        ->groupBy('browser')
        ->get();
    
        $platforms = $data->pluck('browser')->toArray();
        $counts = $data->pluck('jml')->toArray();
        
        return response()->json([
            'statusCode' => 200,
            'data' => [
                'browser'    => $platforms,
                'jmlBrowser' => $counts
            ]
        ], 200);
    }

    /**
     * @return View
     */
    public function dashboard(): View
    {
        $currentDate = Carbon::now();

        $this->setTitle('Dashboard')
            ->setBreadcrumbs([]);

        return view("{$this->viewDirectory()}.dashboard", $this->getData());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function notificationMarkAsRead(Request $request): JsonResponse
    {
        $request->user($this->guardName())->unreadNotifications->markAsRead();
        return response()->json(null);
    }
}
