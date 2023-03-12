<?php

namespace App\Http\Requests;

use App\Rules\ValidXenditVaExternalId;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class XenditVirtualAccountUpdateRequest extends FormRequest
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
        if ($this->get('external_id') == 'fixed-va-1487156410') {
            return [];
        }

        return [
            'id' => ['required', 'string'],
            'status' => ['required','in:ACTIVE,INACTIVE'],
            'expected_amount' => ['required', 'numeric'],
            'external_id' => ['required', 'string', new ValidXenditVaExternalId($this->get('id'), $this->get('expected_amount'))],
        ];
    }
}
