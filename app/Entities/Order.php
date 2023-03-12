<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use App\Entities\Base\BaseModelUUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Order.
 *
 * @package namespace App\Entities;
 */
class Order extends BaseModelUUID
{
    use SoftDeletes;

    protected $fillable = [
        'date',
        'number',
        'invoice_id',
        'user_id',
        'user_address_id',
        'product_id',
        'partner_id',
        'quantity',
        'price',
        'status',
        'notes',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userAddress()
    {
        return $this->belongsTo(UserAddress::class, 'user_address_id');
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
