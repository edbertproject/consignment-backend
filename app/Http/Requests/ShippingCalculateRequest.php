<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ShippingCalculateRequest extends FormRequest
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
            'user_address_id' => ['required', 'exists:user_addresses,id,deleted_at,NULL,user_id,'.Auth::user()->id],
            'product_auction_id' => ['nullable', 'exists:products,id,deleted_at,NULL']
        ];
    }
}
