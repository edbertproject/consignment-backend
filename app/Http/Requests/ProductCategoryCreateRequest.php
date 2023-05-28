<?php

namespace App\Http\Requests;

use App\Rules\CodeRule;
use App\Services\MediaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProductCategoryCreateRequest extends FormRequest
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
            'code' => ['required', 'unique:product_categories,code,NULL,id,deleted_at,NULL', new CodeRule],
            'name' => 'required',
            'photo' => array_merge(['required'], MediaService::fileRule(['image'])),
        ];
    }
}
