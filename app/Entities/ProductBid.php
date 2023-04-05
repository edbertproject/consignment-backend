<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ProductBid.
 *
 * @package namespace App\Entities;
 */
class ProductBid extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'amount',
        'user_id',
        'date_time'
    ];

    public $appends = [
        'user_name'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function getUserNameAttribute() {
        return $this->user->name;
    }
}
