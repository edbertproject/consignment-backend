<?php

namespace App\Http\Requests;

use App\Rules\ValidUserToken;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreatePasswordRequest extends FormRequest
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
            'email' => ['required', 'email', 'exists:users,email,deleted_at,NULL'],
            'password' => ['required', 'string', Password::min(8)->letters()->numbers()->symbols()],
            'token' => ['required', new ValidUserToken($this->get('email'))]
        ];
    }
}
