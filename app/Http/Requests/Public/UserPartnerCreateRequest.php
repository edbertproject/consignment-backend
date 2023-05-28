<?php

namespace App\Http\Requests\Public;

use App\Rules\UsernameRule;
use App\Services\MediaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UserPartnerCreateRequest extends FormRequest
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
            'full_address' => 'required',
            'postal_code' => 'required|integer',
            'province_id' => ['required', 'exists:provinces,id,deleted_at,NULL'],
            'city_id' => ['required', 'exists:cities,id,deleted_at,NULL,province_id,'.$this->request->get('province_id')],
            'district_id' => ['required', 'exists:districts,id,deleted_at,NULL,city_id,'.$this->request->get('city_id')]
        ];
    }
}
