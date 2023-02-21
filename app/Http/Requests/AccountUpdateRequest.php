<?php

namespace App\Http\Requests;

use App\Services\MediaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AccountUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'gender' => 'required|in:Male,Female',
            'photo' => array_merge(['nullable'], MediaService::fileRule(['image'])),
        ];
    }
}
