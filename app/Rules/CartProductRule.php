<?php

namespace App\Rules;

use App\Entities\Cart;
use Illuminate\Contracts\Validation\Rule;

class CartProductRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected $userId,protected $id = null)
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
        return Cart::query()
            ->where('user_id',$this->userId)
            ->where('product_id',$value)
            ->when(!empty($this->id), function ($q) {
                $q->where('id','!=',$this->id);
            })->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Cart with this product already exists';
    }
}
