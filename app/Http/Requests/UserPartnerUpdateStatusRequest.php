<?php

namespace App\Http\Requests;

use App\Rules\UserPartnerUpdateStatusRule;
use App\Utils\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserPartnerUpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'status' => ['required', Rule::in([Constants::PARTNER_STATUS_APPROVED,Constants::PARTNER_STATUS_REJECTED]),
                new UserPartnerUpdateStatusRule($this->id)]
        ];
    }
}
