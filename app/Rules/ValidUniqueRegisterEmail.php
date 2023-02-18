<?php

namespace App\Rules;

use App\Entities\User;
use App\Utils\Constants;
use Illuminate\Contracts\Validation\Rule;

class ValidUniqueRegisterEmail implements Rule
{
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($message = 'The :attribute has already been taken.')
    {
        $this->message = $message;
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
        return User::query()
            ->where('email', $value)
            ->whereHas('roles', function ($q) {
                $q->where('role_id', Constants::ROLE_PUBLIC_ID);
            })->whereNull('deleted_at')
            ->doesntExist();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __($this->message);
    }
}
