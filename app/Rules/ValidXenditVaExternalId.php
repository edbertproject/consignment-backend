<?php

namespace App\Rules;

use App\Entities\Invoice;
use App\Utils\Constants;
use Illuminate\Contracts\Validation\Rule;

class ValidXenditVaExternalId implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected $xenditKey, protected $amount)
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
        //skip xendit test
        if ($value == 'fixed-va-1487156410') {
            return true;
        }

        return Invoice::query()
            ->where('number', $value)
            ->where('xendit_key', $this->xenditKey)
            ->where('grand_total', $this->amount)
            ->where('status', Constants::INVOICE_STATUS_PENDING)
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
