<?php

namespace App\Http\Requests;

use App\Rules\CartProductRule;
use App\Rules\CartQuantityRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CartUpdateRequest extends FormRequest
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
            'product_id' => ['required', 'exists:products,id,deleted_at,NULL', new CartProductRule($this->user_id,$this->id)],
            'quantity' => ['required', 'integer', 'not_in:0', new CartQuantityRule($this->product_id)],
        ];
    }
}
