<?php

namespace App\Models;

use App\Services\HasExportExcel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class Activity extends Model
{
     use HasFactory, HasExportExcel;

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $casts = [
        'additional_data' => 'array'
    ];

    /**
     * @return string
     */
    public function getBrowserWithVersionAttribute()
    {
        return "{$this->browser} {$this->browser_version}";
    }

    /**
     * @return string
     */
    public function getActionHtmlAttribute(): string
    {
        $action = $this->getAttributeValue('action');
        switch ($action) {
            case 'restored':
                $badge = 'info';
                break;
            case 'created':
                $badge = 'success';
                break;
            case 'updated':
                $badge = 'warning';
                break;
            case 'deleted':
                $badge = 'danger';
                break;
            default:
                $badge = 'dark';
        }

        return "<span class='badge badge-{$badge}'>{$action}</span>";
    }

    /**
     * @return array
     */
    public function getUserInfoAttribute(): array
    {
        $user = class_basename($this->getAttributeValue('userable_type'));
        $userId = $this->getAttributeValue('userable_id');
        $userable = $this->userable;
        $role = $userable->role ?? $userable->type ?? 'deleted';

        if (isset($userable->role)) {
            $route = 'admins';
        } else {
            $type = $userable->type ?? '?';
            switch ($type) {
                case 'member':
                    $route = 'users';
                    break;
                default:
                    $route = '';
            }
        }

        $routeName = empty($route) ? null : "admin.{$route}.show";

        return ["{$userable->email} ({$user}:{$userId} - {$role})", $routeName];
    }

    /**
     * @return string
     */
    public function getModelInfoAttribute(): string
    {
        $model = class_basename($this->getAttributeValue('modelable_type'));
        $modelId = $this->getAttributeValue('modelable_id');

        return "{$model}: {$modelId}";
    }

    /**
     * @return MorphTo
     */
    public function userable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return MorphTo
     */
    public function modelable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @param $modelableId
     * @param $modelableType
     * @return JsonResponse
     */
    public function getDatatables($modelableId = null, $modelableType = null): JsonResponse
    {
        $period = request('filter_value');
        $type = request('filter_type');
        $query = $this->newQuery()
            ->whereHasMorph('userable', User::class)
            ->when(
                $modelableId,
                function (Builder $builder, $modelableId) use ($modelableType) {
                    return $builder->where(function ($builder) use ($modelableType, $modelableId) {
                            $builder->where('modelable_type', $modelableType)
                                ->where('modelable_id', $modelableId);
                        });
                })
            ->when($type, function (Builder $builder, $type) {
                return $builder->where('action', $type);
            })
            ->when($period, function (Builder $builder, $period) {
                $period = explode(' - ', $period);
                list($startedAt, $endedAt) = $period;
                return $builder->whereRaw("DATE(created_at) BETWEEN '$startedAt' AND '$endedAt'");
            })
            ->with('userable');

        $totals = $this->newQuery()
            ->whereHasMorph('userable', User::class)
            ->when(
                $modelableId,
                function (Builder $builder, $modelableId) use ($modelableType) {
                    return $builder->where(function ($builder) use ($modelableType, $modelableId) {
                        $builder->where('modelable_type', $modelableType)
                            ->where('modelable_id', $modelableId);
                    });
                })
            ->when($type, function (Builder $builder, $type) {
                return $builder->where('action', $type);
            })
            ->when($period, function (Builder $builder, $period) {
                $period = explode(' - ', $period);
                list($startedAt, $endedAt) = $period;
                return $builder->whereRaw("DATE(created_at) BETWEEN '$startedAt' AND '$endedAt'");
            })
            ->selectRaw('COUNT(DISTINCT userable_id) AS total')
            ->value('total');

        return DataTables::eloquent($query)
            ->editColumn('browser', function (self $activity) {
                return $activity->browserWithVersion;
            })
            ->editColumn('created_at', function (self $activity) {
                return '<div class="small text-muted">'.$activity->created_at->diffForHumans().'</div><div>'.$activity->created_at->toDatetimeString().'</div>';
            })
            ->editColumn('action', function (self $activity) {
                return $activity->actionHtml;
            })
            ->addColumn('user', function (self $activity) {
                list($user, $routeName) = $activity->userInfo;
                $route = is_null($routeName) ? '#' : route($routeName, $activity->userable_id);
                return "<a class='btn-link' href='". $route ."'>{$user}</a>";
            })
            ->addColumn('model', function (self $activity) {
                return $activity->modelInfo;
            })
            ->addColumn('actions', function (self $activity) {
                return '<button type="button" class="btn btn-light" data-title="Request ID: '.$activity->id.'" data-json="'.htmlentities(json_encode($activity->additional_data)).'" data-toggle="modal" data-target="#modalViewDetail" href="javascript:void(0)" title="Details"><i class="fe fe-eye text-blue"></i></button>';
            })
            ->rawColumns(['action', 'created_at', 'actions', 'user'])
            ->with('chart_browser', function () use ($modelableId, $modelableType, $period, $type, $totals) {
                $activities = $this->newQuery()
                    ->whereHasMorph('userable', User::class)
                    ->selectRaw("browser, COUNT(DISTINCT userable_id) AS total")
                    ->when($modelableId, function ($builder, $modelableId) use ($modelableType) {
                        return $builder->where(function ($builder) use ($modelableType, $modelableId) {
                                $builder->where('modelable_type', $modelableType)
                                    ->where('modelable_id', $modelableId);
                            });
                    })
                    ->when($type, function (Builder $builder, $type) {
                        return $builder->where('action', $type);
                    })
                    ->when($period, function (Builder $builder, $period) {
                        $period = explode(' - ', $period);
                        list($startedAt, $endedAt) = $period;
                        return $builder->whereRaw("DATE(created_at) BETWEEN '$startedAt' AND '$endedAt'");
                    })
                    ->groupBy("browser")
                    ->get()
                    ->map(function ($item) {
                        return [$item->browser, $item->total];
                    });

                $colors = $activities->randomColor();

                return [$activities->all(), $colors->all(), $totals];
            })
            ->with('chart_platform', function () use ($modelableId, $modelableType, $period, $type, $totals) {
                $activities = $this->newQuery()
                    ->whereHasMorph('userable', User::class)
                    ->selectRaw("platform, COUNT(DISTINCT userable_id) AS total")
                    ->when($modelableId, function ($builder, $modelableId) use ($modelableType) {
                        return $builder->where(function ($builder) use ($modelableType, $modelableId) {
                                $builder->where('modelable_type', $modelableType)
                                    ->where('modelable_id', $modelableId);
                            });
                    })
                    ->when($type, function (Builder $builder, $type) {
                        return $builder->where('action', $type);
                    })
                    ->when($period, function (Builder $builder, $period) {
                        $period = explode(' - ', $period);
                        list($startedAt, $endedAt) = $period;
                        return $builder->whereRaw("DATE(created_at) BETWEEN '$startedAt' AND '$endedAt'");
                    })
                    ->groupBy("platform")
                    ->get()
                    ->map(function ($item) {
                        return [$item->platform, $item->total];
                    });

                $colors = $activities->randomColor();

                return [$activities->all(), $colors->all(), $totals];
            })
            ->toJson();
    }

    /**
     * @return JsonResponse
     */
    public function accesDatatables(): JsonResponse
    {
        return DataTables::eloquent($this->query())
            ->editColumn('browser', function (self $access) {
                return "<code>".$access->browser .' - Version : '. $access->browser_version."</code>";
            })
            ->editColumn('updated_at', function (self $access) {
                return '<div class="small text-muted">'. diffHumans($access->updated_at) .'</div><div>'.formatSetTimezone($access->updated_at).'</div>';
            })
            ->addColumn('actions', function(self $access) {
                $actionButton = '';
                $actionButton .= '<a class="btn btn-light" title="Edit"><i class="fas fa-eye text-primary ml-2"></i></a>';

                return $actionButton ? $actionButton : '#';
            })
            ->rawColumns(['updated_at', 'browser', 'actions'])
            ->toJson();
    }
}
