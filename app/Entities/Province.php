<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Province.
 *
 * @package namespace App\Entities;
 */
class Province extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];


    public function cities() {
        return $this->hasMany(City::class);
    }
}
