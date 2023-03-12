<?php

namespace App\Entities;

use App\Entities\Base\BaseModelUUID;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Invoice.
 *
 * @package namespace App\Entities;
 */
class Invoice extends BaseModelUUID
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'date',
        'number',
        'payment_method_id',
        'payment_number',
        'courier_code',
        'courier_service',
        'courier_esd',
        'subtotal',
        'tax_amount',
        'admin_fee',
        'platform_fee',
        'courier_cost',
        'grand_total',
        'status',
        'xendit_key',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
