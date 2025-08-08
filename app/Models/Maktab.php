<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use DB;

class Maktab extends Model
{
    use HasFactory;

     /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'host_data' => 'array',
        'is_available' => 'boolean',
    ];

    /**
     * @return JsonResponse
     */
    public function getDatatables(): JsonResponse
    {
        $query = $this->query()->from('maktabs as m')
                ->join('sectors as s', 's.id', '=', 'm.sector_id')
                ->join('coordinators as c', 'c.id', '=', 'm.coordinator_id')
                ->select([
                    'm.id',
                    'c.name as cp_name',
                    'c.phone as cp_phone',
                    's.sektor',
                    'm.type',
                    'm.is_available',
                    'm.updated_at',
                    DB::raw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.address')) as address"),
                    DB::raw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.owner')) as owner"),
                    DB::raw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.phone')) as phone"),
                    DB::raw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.capacity')) as capacity")
                ]);
        
        return DataTables::eloquent($query)
            ->editColumn('updated_at', function (self $category) {
                return '<div class="small text-muted">'. diffHumans($category->updated_at) .'</div><div>'.formatSetTimezone($category->updated_at).'</div>';
            })
            ->editColumn('is_available', function (self $product) {
                return $product->is_available
                    ? '<span class="badge bg-success">Available</span>'
                    : '<span class="badge bg-danger">Not Available</span>';;
            })
            ->editColumn('address', function ($row) {
                return $row->address ?? '-';
            })
            ->editColumn('owner', function ($row) {
                return $row->owner ?? '-';
            })
            ->filterColumn('owner', function($query, $keyword) {
                $query->whereRaw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.owner')) LIKE ?", ["%{$keyword}%"]);
            })
            ->orderColumn('owner', function ($query, $order) {
                $query->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.owner')) {$order}");
            })
            ->addColumn('phone', function ($row) {
                return $row->phone ?? '-';
            })
            ->addColumn('capacity', function ($row) {
                return $row->capacity ?? '-';
            })
            ->editColumn('type', function ($row) {
                return $row->type ?? '-';
            })
            ->addColumn('actions', function (self $category) {
                $actionButton = '';

                $actionButton .= '<a class="btn btn-light btn-sm" href="'.route('admin.maktabs.edit', [$category->id]).'" title="Edit"><i class="bi bi-pencil-square text-warning"></i></a>';
                $actionButton .= '<a class="btn btn-light btn-sm delete-item" href="'.route('admin.maktabs.destroy', [$category->id]).'" title="Delete"><i class="bi bi-trash-fill text-danger"></i></a>';
                
                return $actionButton ? $actionButton : '#';
            })
            ->rawColumns(['actions', 'type', 'capacity', 'phone', 'owner', 'address', 'is_available', 'updated_at'])
            ->toJson();
    }
}
