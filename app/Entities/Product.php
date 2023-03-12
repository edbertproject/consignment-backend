<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
        'available_quantity',
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
        'status',
        'partner_id'
    ];

    /**
     * @return MorphMany
     */
    public function photos()
    {
        return $this->morphMany(Media::class, 'model')
            ->where('collection_name', 'photos');
    }

    public function productCategory() {
        return $this->belongsTo(ProductCategory::class);
    }

    public function partner() {
        return $this->belongsTo(Partner::class);
    }
}
