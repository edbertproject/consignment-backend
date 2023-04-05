<?php

namespace App\Http\Requests\Public;

use App\Rules\Public\ProductBidAmountRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductBidRequest extends FormRequest
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
            'amount' => ['required', 'integer', 'not_in:0', new ProductBidAmountRule($this->id)]
        ];
    }
}
