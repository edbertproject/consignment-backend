<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;

/**
 * Class District.
 *
 * @package namespace App\Entities;
 */
class District extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

    public function city() {
        return $this->belongsTo(City::class);
    }
}
