<?php

namespace App\Http\Requests;

use App\Rules\NotPresent;
use App\Services\MediaService;
use App\Utils\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProductCreateRequest extends FormRequest
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
            'product_category_id' => ['required', 'exists:product_categories,id,deleted_at,NULL'],
            'name' => ['required', 'max:256'],
            'type' => ['required', Rule::in(Constants::PRODUCT_TYPES)],
            'price' => ['required_if:status,'.Constants::PRODUCT_TYPE_CONSIGN, 'integer', 'min:0'],
            'start_price' => ['required_if:status,'.Constants::PRODUCT_TYPE_AUCTION, 'integer', 'min:0'],
            'multiplied_price' => ['required_if:status,'.Constants::PRODUCT_TYPE_AUCTION, 'integer', 'min:0'],
            'desired_price' => ['required_if:status,'.Constants::PRODUCT_TYPE_AUCTION, 'integer', 'min:'.$this->request->get('start_price')],
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
            'photos' => ['required','array','min:2'],
            'photos.*' => array_merge(['required'], MediaService::fileRule(['image'])),
            'cancel_reason' => ['sometimes', new NotPresent],
            'status' => ['sometimes', new NotPresent],
        ];
    }
}
