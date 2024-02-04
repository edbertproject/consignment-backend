<?php

namespace App\Entities;

use App\Entities\Base\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class OrderStatus.
 *
 * @package namespace App\Entities;
 */
class OrderStatus extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['order_id','status','type'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
