<?php

namespace App\Rules;

use App\Entities\Cart;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderCheckCartRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $cart = Cart::query()
            ->select(
                DB::raw('IFNULL(products.partner_id,"ADMIN") AS partner')
            )->join('products','products.id','carts.product_id')
            ->whereIn('carts.id',$value)
            ->pluck('partner')
            ->all();

        return count(array_unique($cart)) < 2;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Product is from different seller, please separate checkout from different seller.';
    }
}
