<?php

namespace App\Http\Requests\Public;

use App\Rules\OrderCheckCartRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderCheckRequest extends FormRequest
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
//            'cart_ids' => ['required', new OrderCheckCartRule],
//            'cart_ids.*' => ['distinct', 'exists:carts,id'],
        ];
    }
}
