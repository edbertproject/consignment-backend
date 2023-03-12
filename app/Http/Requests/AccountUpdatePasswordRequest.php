<?php

namespace App\Http\Requests;

use App\Rules\ValidPassword;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AccountUpdatePasswordRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'old_password' => [
                'required',
                'string',
                new ValidPassword()
            ],
            'password' => ['required', 'string', 'confirmed', Password::min(8)->letters()->numbers()->symbols()]
        ];
    }

    public function attributes()
    {
        return [
            'old_password' => 'old password'
        ];
    }
}
