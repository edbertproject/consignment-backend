<?php

namespace App\Rules\Public;

use App\Entities\Product;
use Illuminate\Contracts\Validation\Rule;

class ProductBidAmountRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected $productId)
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
        $product = Product::find($this->productId);
        return $value > $product->start_price &&
            ($value - $product->start_price) % $product->multiplied_price === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The amount is invalid.';
    }
}
