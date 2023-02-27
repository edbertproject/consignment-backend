<?php

namespace App\Rules;

use App\Entities\User;
use App\Utils\Constants;
use Illuminate\Contracts\Validation\Rule;

class UserPartnerStatusRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected $id)
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
        $partner = User::find($this->id)->partner;

        return $partner->status === Constants::PARTNER_STATUS_WAITING_APPROVAL;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Status can not be updated.';
    }
}
