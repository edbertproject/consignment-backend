<?php

namespace App\Http\Requests;

use App\Rules\UsernameRule;
use App\Rules\ValidUniqueRegisterEmail;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => ['required', 'string'],
            'username' => ['required', 'string' , 'unique:users,username,NULL,id,deleted_at,NULL' , new UsernameRule],
            'email' => ['required', 'email', new ValidUniqueRegisterEmail()],
            'phone_number' => ['required', 'numeric'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()->symbols()]
        ];

        return $rules;
    }
}
