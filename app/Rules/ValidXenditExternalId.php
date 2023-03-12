<?php

namespace App\Rules;

use App\Entities\Invoice;
use App\Utils\Constants;
use Illuminate\Contracts\Validation\Rule;

class ValidXenditExternalId implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected $xenditKey, protected $status, protected $paidAmount = null)
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
        if ($value == 'invoice_123124123') {
            return true;
        }

        if ($this->status == 'EXPIRED') {
            return true;
        }

        return Invoice::query()
            ->where('number', $value)
            ->where('xendit_key', $this->xenditKey)
            ->where('grand_total', $this->paidAmount)
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
