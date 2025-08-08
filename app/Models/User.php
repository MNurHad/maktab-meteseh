<?php

namespace App\Models;

use App\Services\HasExportExcel;
use App\Services\HasFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles;

     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

     /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

     /**
     * @return string
     */
    public function guardName(): string
    {
        return 'admin';
    }

    /**
     * @return mixed
     */
    public function getRoleAttribute()
    {
        return $this->getRoleNames()
            ->first();
    }

    /**
     * @return array
     */
    public function getAllRoles()
    {
        return Role::where('name', '!=', 'admin')
            ->select('name', 'slug')
            ->get();
    }

    /**
     * @param Builder $builder
     * @return Builder
     */
    public function scopeHasNotificationPermission(Builder $builder): Builder
    {
        return $builder->whereHas('roles', function (Builder $builder) {
            $builder->whereHas('permissions', function (Builder $builder) {
                $builder->where('name', 'applications.update');
            });
        });
    }

}
