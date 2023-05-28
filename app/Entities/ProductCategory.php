<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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

    public function products() {
        return $this->hasMany(Product::class);
    }

    /**
     * @return MorphOne
     */
    public function photo()
    {
        return $this->morphOne(Media::class, 'model')
            ->where('collection_name', 'photo')
            ->orderBy('id');
    }

}
