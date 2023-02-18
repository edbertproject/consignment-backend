<?php

namespace App\Rules;

use App\Entities\User;
use App\Utils\Constants;
use Illuminate\Contracts\Validation\Rule;

class ValidForgotPasswordEmail implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected $isAdmin = 0)
    {
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $values
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return User::query()
            ->where('email', $value)
            ->whereHas('roleUser', function ($query) {
                $query->when(!$this->isAdmin, function ($queryWhen) {
                    $queryWhen->whereIn('role_id', [
                        Constants::ROLE_PUBLIC,
                        Constants::ROLE_PARTNER,
                    ]);
                }, function ($queryWhen) {
                    $queryWhen->whereNotIn('role_id', [
                        Constants::ROLE_PUBLIC,
                        Constants::ROLE_PARTNER,
                    ]);
                });
            })->whereNull('deleted_at')
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.exists');
    }
}
