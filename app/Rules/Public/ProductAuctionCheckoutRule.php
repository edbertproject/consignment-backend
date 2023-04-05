<?php

namespace App\Rules\Public;

use App\Entities\Product;
use App\Utils\Constants;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProductAuctionCheckoutRule implements Rule
{
    protected $message;
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
        return Product::query()
            ->where('id',$value)
            ->where('type','!=',Constants::PRODUCT_TYPE_CONSIGN)
            ->where('winner_id',Auth::id())
            ->whereRaw('end_date <= DATE_ADD(end_date, INTERVAL ? HOUR)',[Constants::PRODUCT_AUCTION_EXPIRES])
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Product auction is invalid to checkout.';
    }
}
