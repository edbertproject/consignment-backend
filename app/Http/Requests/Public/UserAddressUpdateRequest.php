<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserAddressUpdateRequest extends FormRequest
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
            'label' => 'required|string|unique:user_addresses,label,'.$this->id.',id,deleted_at,NULL,user_id,'.Auth::id(),
            'receiver_name' => 'required|string',
            'phone_number' => 'required|regex:/(08)[0-9]/',
            'full_address' => 'required|min:50',
            'postal_code' => 'required|numeric',
            'province_id' => 'required|exists:provinces,id,deleted_at,NULL',
            'city_id' => 'required|exists:cities,id,deleted_at,NULL,province_id,'.$this->province_id,
            'district_id' => 'required|exists:districts,id,deleted_at,NULL,city_id,'.$this->city_id,
            'note' => 'nullable',
            'is_primary' => 'required|boolean',
        ];
    }
}
