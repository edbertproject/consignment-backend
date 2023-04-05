<?php

namespace App\Http\Requests;

use App\Rules\NotPresent;
use App\Utils\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductUpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return !Auth::user()->hasRole(Constants::ROLE_PARTNER_ID);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'status' => ['required', Rule::in([
                Constants::PRODUCT_STATUS_APPROVED,
                Constants::PRODUCT_STATUS_CANCEL_APPROVED,
                Constants::PRODUCT_STATUS_REJECTED
            ])
            ],
        ];
    }
}
