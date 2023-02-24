<?php

namespace App\Http\Requests;

use App\Rules\UserInternalRoleRule;
use App\Rules\UsernameRule;
use App\Rules\ValidUniqueRegisterEmail;
use App\Services\MediaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class UserInternalUpdateRequest extends FormRequest
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
            'username' => ['required', 'string' , 'unique:users,username,'.$this->id.',id,deleted_at,NULL' , new UsernameRule],
            'email' => ['required', 'email', 'unique:users,email,'.$this->id.',id,deleted_at,NULL'],
            'phone_number' => ['required', 'numeric'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()->symbols()],
            'photo' => array_merge(['nullable'], MediaService::fileRule(['image'])),
            'role_id' => ['required', 'exists:roles,id,deleted_at,NULL', new UserInternalRoleRule],
        ];
    }
}
