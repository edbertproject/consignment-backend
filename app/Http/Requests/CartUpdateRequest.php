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
        $rules = [
            'carts' => 'array|min:0',
            'carts.*.product_id' => ['required', 'distinct', 'exists:products,id,deleted_at,NULL'],
            'carts.*.quantity' => ['required', 'integer', 'not_in:0'],
        ];

//        foreach ($this->request->get('carts') as $index => $cart) {
//            $rules['carts.' . $index . '.product_id'] = ['required', 'distinct', 'exists:products,id,deleted_at,NULL'];
//            $rules['carts.' . $index . '.quantity'] = ['required', 'integer', 'not_in:0', new CartQuantityRule($cart->product_id)];
//        }

        return $rules;
    }
}
