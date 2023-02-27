<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;

/**
 * Class Partner.
 *
 * @package namespace App\Entities;
 */
class Partner extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'full_address',
        'postal_code',
        'province_id',
        'city_id',
        'district_id',
        'status'
    ];
}
