<?php

namespace App\Rules;

use App\Utils\Constants;
use Illuminate\Contracts\Validation\Rule;

class UserInternalRoleRule implements Rule
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
        return !in_array($value, [
            Constants::ROLE_PARTNER_ID,
            Constants::ROLE_PUBLIC_ID
        ]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Role is forbidden.';
    }
}
