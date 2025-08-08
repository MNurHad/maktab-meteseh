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

class Group extends Model
{
    // 
    use HasFactory;

     /**
     * @var array
     */
    protected $guarded = ['id'];

     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'assign_maktabs';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'group_data' => 'array',
        'vehicle_data' => 'array',
    ];

     /**
     * @return JsonResponse
     */
    public function getDatatables(): JsonResponse
    {
        $query = $this->query()->from('assign_maktabs as g')
            ->join('maktabs as m', 'g.maktab_id', '=', 'm.id')
            ->join('sectors as s', 's.id', '=', 'm.sector_id')
            ->join('coordinators as c', 'c.id', '=', 'm.coordinator_id')
            ->select([
                'g.id',
                'g.leader',
                'g.phone_leader',
                'c.name as cp_name',
                'c.phone as cp_phone',
                's.sektor',
                'g.planing_at',
                'g.actual_at',
                'g.updated_at',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.address')) as address"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.owner')) as owner"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(m.host_data, '$.phone')) as phone"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(g.group_data, '$.provincy')) as provincy"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(g.group_data, '$.city')) as city"),
            ]);

        return DataTables::eloquent($query)
            ->editColumn('updated_at', function ($row) {
                return '<div class="small text-muted">'. diffHumans($row->updated_at) .'</div><div>'. formatSetTimezone($row->updated_at) .'</div>';
            })
            ->editColumn('address', function ($row) {
                return $row->address ?? '-';
            })
            ->addColumn('owner', function ($row) {
                $owner = $row->owner ?? '-';
                $phone = $row->phone ?? '-';
                return "$owner<br><small class=\"text-muted\">$phone</small>";
            })
            ->addColumn('group_location', function ($row) {
                return ($row->provincy ?? '-') . ' / ' . ($row->city ?? '-');
            })
            ->addColumn('periode', function ($row) {
                return date('d M Y H:i', strtotime($row->planing_at)) .'-'. date('d M Y H:i', strtotime($row->actual_at)); 
            })
            ->editColumn('leader', function ($row) {
                $owner = $row->leader ?? '-';
                $phone = $row->phone_leader ?? '-';
                return "$owner<br><small class=\"text-muted\">$phone</small>";
            })
            ->addColumn('actions', function ($row) {
                return 
                    '<a class="btn btn-light btn-sm" href="'. route('admin.groups.edit', [$row->id]) .'" title="Edit">
                        <i class="bi bi-pencil-square text-warning"></i>
                    </a>
                    <a class="btn btn-light btn-sm delete-item" href="'. route('admin.groups.destroy', [$row->id]) .'" title="Delete">
                        <i class="bi bi-trash-fill text-danger"></i>
                    </a>';
            })
            ->rawColumns(['actions', 'owner', 'leader', 'periode', 'group_location', 'address', 'updated_at'])
            ->toJson();
    }
}
