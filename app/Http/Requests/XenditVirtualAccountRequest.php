<?php

namespace App\Http\Requests;

use App\Rules\ValidXenditVaExternalId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class XenditVirtualAccountRequest extends FormRequest
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
            'amount' => ['required', 'numeric'],
            'callback_virtual_account_id' => ['required', 'string'],
            'external_id' => ['required', 'string', new ValidXenditVaExternalId($this->get('callback_virtual_account_id'), $this->get('amount'))],
        ];
    }
}
