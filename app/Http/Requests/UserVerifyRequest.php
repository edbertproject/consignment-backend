<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserVerifyRequest extends FormRequest
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
            'token' => ['required', 'string', 'exists:user_tokens,token,deleted_at,NULL']
        ];
    }
}
