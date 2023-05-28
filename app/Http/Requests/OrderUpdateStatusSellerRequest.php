<?php

namespace App\Http\Requests;

use App\Utils\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class OrderUpdateStatusSellerRequest extends FormRequest
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
            'status' => ['required', Rule::in([
                Constants::ORDER_SELLER_STATUS_PROCESSING,
                Constants::ORDER_SELLER_STATUS_CANCELED,
                Constants::ORDER_SELLER_STATUS_ON_DELIVERY
            ])],
        ];
    }
}
