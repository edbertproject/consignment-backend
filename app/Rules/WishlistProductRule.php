<?php

namespace App\Rules;

use App\Entities\Wishlist;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class WishlistProductRule implements Rule
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
        return Wishlist::query()
            ->where('user_id', Auth::id())
            ->where('product_id',$value)
            ->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Product already in wishlist.';
    }
}
