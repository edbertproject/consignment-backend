<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserAddress.
 *
 * @package namespace App\Entities;
 */
class UserAddress extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'label',
        'receiver_name',
        'phone_number',
        'full_address',
        'postal_code',
        'province_id',
        'city_id',
        'district_id',
        'note',
        'is_primary',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

}
