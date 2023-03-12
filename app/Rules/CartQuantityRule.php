<?php

namespace App\Rules;

use App\Entities\Product;
use Illuminate\Contracts\Validation\Rule;

class CartQuantityRule implements Rule
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

        return $value <= $product->available_quantity;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Quantity can not be more than available quantity.';
    }
}
