<?php

namespace App\Http\Requests;

use App\Rules\ValidXenditExternalId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class XenditInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => ['required', 'string'],
            'status' => ['required','in:EXPIRED,PAID'],
            'paid_amount' => ['required_if:status,PAID', 'numeric'],
            'external_id' => ['required', 'string', new ValidXenditExternalId($this->get('callback_virtual_account_id'),$this->get('status'), $this->get('paid_amount'))],
        ];
    }
}
