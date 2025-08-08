<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Village extends Model
{
     /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'indonesia_villages';

    /**
     * @var array
     */
    protected $guarded = ['id'];

    public function district()
    {
        return $this->belongsTo(District::class, 'city_code', 'code');
    }
}
