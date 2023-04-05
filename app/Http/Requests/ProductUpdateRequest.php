<?php

namespace App\Http\Requests;

use App\Rules\NotPresent;
use App\Rules\ProductDesiredPriceRule;
use App\Utils\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
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
        $type = Constants::PRODUCT_TYPES;

        if (Auth::user()->hasRole(Constants::ROLE_PARTNER_ID)) {
            $type = [Constants::PRODUCT_TYPE_AUCTION, Constants::PRODUCT_TYPE_CONSIGN];
        }

        $conditionalRules = [];

        if ($this->type === Constants::PRODUCT_TYPE_SPECIAL_AUCTION) {
            $conditionalRules['participant'] = ['required', 'integer', 'min:5'];
        }

        if ($this->type === Constants::PRODUCT_TYPE_CONSIGN) {
            $conditionalRules['price'] = ['required', 'integer', 'min:0'];
        }

        if (in_array($this->type,[Constants::PRODUCT_TYPE_AUCTION, Constants::PRODUCT_TYPE_SPECIAL_AUCTION])) {
            $conditionalRules['start_price'] = ['required', 'integer', 'min:0'];
            $conditionalRules['multiplied_price'] = ['required', 'integer', 'min:0'];
            $conditionalRules['desired_price'] = ['required', 'integer', 'min:'.$this->start_price, new ProductDesiredPriceRule($this->start_price,$this->multiplied_price)];
        }

        return array_merge([
            'product_category_id' => ['required', 'exists:product_categories,id,deleted_at,NULL'],
            'name' => ['required', 'max:256'],
            'type' => ['required', Rule::in($type)],
            'participant' => ['sometimes', new NotPresent],
            'price' => ['sometimes', new NotPresent],
            'start_price' => ['sometimes', new NotPresent],
            'multiplied_price' => ['sometimes', new NotPresent],
            'desired_price' => ['sometimes', new NotPresent],
            'start_date' => 'required|date_format:Y-m-d H:i',
            'end_date' => 'nullable|date_format:Y-m-d H:i',
            'weight' => 'required|numeric|min:0|not_in:0',
            'quantity' => 'required|integer|min:0|not_in:0',
            'long_dimension' => 'required|numeric|min:0|not_in:0',
            'wide_dimension' => 'required|numeric|min:0|not_in:0',
            'high_dimension' => 'required|numeric|min:0|not_in:0',
            'condition' => ['required', Rule::in(Constants::PRODUCT_CONDITIONS)],
            'warranty' => ['required', Rule::in(Constants::PRODUCT_WARRANTIES)],
            'description' => 'nullable|string',
            'cancel_reason' => ['sometimes', new NotPresent],
            'status' => ['sometimes', new NotPresent],
        ],$conditionalRules);
    }
}
