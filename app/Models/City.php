<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Cache;

class City extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'indonesia_cities';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    public function province()
    {
        return $this->belongsTo(Provincy::class, 'province_code', 'code');
    }

    public function getDatatables($code): JsonResponse
    {
        $key = 'cities_'.$code;

        $data = Cache::rememberForever($key, function () use ($code) {
            return $this->query()
                    ->where('province_code', $code)
                    ->orderBy('name')
                    ->get()
                    ->map(function ($item) {
                        $item->name = ucwords(strtolower($item->name));
                        return $item;
                    });
        });
        
        return DataTables::of($data)
            ->editColumn('is_published', function (self $provincy) {
                return $provincy->is_published
                    ? '<span class="badge rounded-pill bg-success">Published</span>'
                    : '<span class="badge rounded-pill bg-danger">Not Published</span>';;
            })
            ->editColumn('name', function (self $provincy) {
                return ucfirst($provincy->name);
            })
            ->editColumn('updated_at', function (self $provincy) {
                return '<div class="small text-muted">'. diffHumans($provincy->updated_at) .'</div><div>'.formatSetTimezone($provincy->updated_at).'</div>';
            })
            ->addColumn('actions', function (self $provincy) {
                $actionButton = '';

                $actionButton .= '<a class="btn btn-light btn-sm" href="'.route('admin.provincies.show', [$provincy->getKey()]).'" title="Show"><i class="far fa-eye text-warning"></i></a>';
                
                return $actionButton ? $actionButton : '#';
            })
            ->rawColumns(['actions', 'name', 'updated_at', 'is_published'])
            ->toJson();
    }
}
