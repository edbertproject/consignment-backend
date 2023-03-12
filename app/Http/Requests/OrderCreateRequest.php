<?php

namespace App\Http\Requests;

use App\Entities\PaymentMethod;
use App\Utils\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class OrderCreateRequest extends FormRequest
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
        $rules = [];

        $rules['payment_method_id'] = ['required', 'exists:payment_methods,id,deleted_at,NULL,is_enabled,1'];
        $rules['user_address_id'] = ['required', 'exists:user_addresses,id,deleted_at,NULL,user_id,'.Auth::user()->id];

        $rules['courier_code'] = 'required|in:JNE,POS,TIKI';
        $rules['courier_service'] = 'required|string';
        $rules['courier_cost'] = 'required|integer';
        $rules['courier_esd'] = 'required|string';

        $isCreditDebitCard = PaymentMethod::query()
            ->where('id', $this->get('payment_method_id'))
            ->where('type', Constants::PAYMENT_METHOD_TYPE_CREDIT_CARD)
            ->exists();

        if ($isCreditDebitCard) {
            $rules['xendit_token_id'] = 'required|string';
            $rules['xendit_authentication_id'] = 'required|string';
            $rules['xendit_card_cvn'] = 'required|integer|digits:3';
        }

        $orders = $this->get('orders', []);

        if (is_array($orders)) {
            $rules['cart_ids'] = ['required'];
            $rules['cart_ids.*'] = ['distinct', 'exists:carts,id'];
        }

        return $rules;
    }
}
