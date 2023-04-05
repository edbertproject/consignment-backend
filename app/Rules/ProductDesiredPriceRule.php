<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ProductDesiredPriceRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected $startPrice, protected $multiplied)
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
        return ($value - $this->startPrice) % $this->multiplied === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Desired price must be multiplied from start price.';
    }
}
