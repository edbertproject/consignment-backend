<?php

namespace App\Http\Requests;

use App\Rules\CartProductRule;
use App\Rules\CartQuantityRule;
use App\Rules\NotPresent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CartCreateRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id,deleted_at,NULL',
            'product_id' => ['required', 'exists:products,id,deleted_at,NULL', new CartProductRule($this->user_id)],
            'quantity' => ['required', 'integer', 'not_in:0', new CartQuantityRule($this->product_id)],
            'is_available' => ['sometimes', new NotPresent]
        ];
    }
}
