<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Cart.
 *
 * @package namespace App\Entities;
 */
class Cart extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'product_id', 'quantity', 'is_available'
    ];

    protected $appends = [
        'subtotal'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }

    public function getSubtotalAttribute()
    {
        $product = $this->product()->first();
        return $product->price * $this->quantity;
    }
}
