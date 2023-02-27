<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Product.
 *
 * @package namespace App\Entities;
 */
class Product extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_category_id',
        'type',
        'name',
        'price',
        'start_price',
        'multiplied_price',
        'desired_price',
        'start_date',
        'end_date',
        'weight',
        'quantity',
        'long_dimension',
        'wide_dimension',
        'high_dimension',
        'condition',
        'warranty',
        'description',
        'cancel_reason',
        'status'
    ];

    public function productCategory() {
        return $this->belongsTo(ProductCategory::class);
    }
}
