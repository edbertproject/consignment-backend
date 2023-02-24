<?php

namespace App\Http\Requests;

use App\Rules\UserInternalRoleRule;
use App\Rules\UsernameRule;
use App\Rules\UserPartnerRoleRule;
use App\Rules\ValidUniqueRegisterEmail;
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
            'name' => ['required', 'string'],
            'username' => ['required', 'string' , 'unique:users,username,NULL,id,deleted_at,NULL' , new UsernameRule],
            'email' => ['required', 'email', 'unique:users,email,NULL,id,deleted_at,NULL'],
            'phone_number' => ['required', 'numeric'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()->symbols()],
            'full_address' => 'required',
            'postal_code' => 'required|integer',
            'province_id' => ['required', 'exists:provinces,id,deleted_at,NULL'],
            'city_id' => ['required', 'exists:cities,id,deleted_at,NULL,province_id,'.$this->request->get('province_id')],
            'district_id' => ['required', 'exists:districts,id,deleted_at,NULL,city_id,'.$this->request->get('city_id')],
            'photo' => array_merge(['nullable'], MediaService::fileRule(['image'])),
        ];
    }
}
