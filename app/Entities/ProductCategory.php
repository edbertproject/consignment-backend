<?php

namespace App\Entities;

use App\Entities\Interfaces\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductCategory.
 *
 * @package namespace App\Entities;
 */
class ProductCategory extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'name'
    ];
}
