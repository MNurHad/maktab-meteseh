<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'indonesia_districts';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_code', 'code');
    }
}
