<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PaymentMethodInstruction.
 *
 * @package namespace App\Entities;
 */
class PaymentMethodInstruction extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'payment_method_id',
        'title',
        'instructions',
    ];

    protected $casts = [
        'instructions' => 'array',
    ];

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

}
