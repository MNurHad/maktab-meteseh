<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class Coordinator extends Model
{
    use HasFactory;

     /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @return JsonResponse
     */
    public function getDatatables(): JsonResponse
    {
        $query = $this->query()
                ->join('sectors', 'sectors.id', '=', 'coordinators.sector_id')
                ->select('coordinators.*', 'sectors.sektor');
        
        return DataTables::eloquent($query)
            ->editColumn('updated_at', function (self $category) {
                return '<div class="small text-muted">'. diffHumans($category->updated_at) .'</div><div>'.formatSetTimezone($category->updated_at).'</div>';
            })
            ->addColumn('actions', function (self $category) {
                $actionButton = '';

                $actionButton .= '<a class="btn btn-light btn-sm" href="'.route('admin.coordinators.edit', [$category->id]).'" title="Edit"><i class="bi bi-pencil-square text-warning"></i></a>';
                $actionButton .= '<a class="btn btn-light btn-sm delete-item" href="'.route('admin.coordinators.destroy', [$category->id]).'" title="Delete"><i class="bi bi-trash-fill text-danger"></i></a>';
                
                return $actionButton ? $actionButton : '#';
            })
            ->rawColumns(['actions', 'updated_at'])
            ->toJson();
    }
}
