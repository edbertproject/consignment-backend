<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Product.
 *
 * @package namespace App\Entities;
 */
class Product extends Model implements Transformable
{
    use TransformableTrait;

    const CONDITION_BNIB = "Brand New In Box";
    const CONDITION_BNOB = "Brand New In Box";
    const CONDITION_VGOOD = "Very Good Condition";
    const CONDITION_GOOD = "Good Condition";
    const CONDITION_JUDGE = "Judge By Picture";

    const WARRANTY_ON = "On";
    const WARRANTY_OFF = "Off";

    const TYPE_CONSIGN = "Consign";
    const TYPE_AUCTION = "Auction";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

}
