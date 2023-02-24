<?php

namespace App\Http\Requests;

use App\Rules\CodeRule;
use App\Rules\NotPresent;
use App\Utils\Constants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RoleCreateRequest extends FormRequest
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
            'code' => ['required', 'unique:roles,code,NULL,id,deleted_at,NULL',
                'not_in:' . implode(",",[Constants::ROLE_SUPER_ADMIN_CODE, Constants::ROLE_PARTNER_CODE, Constants::ROLE_PUBLIC_CODE]),
                new CodeRule(uppercase: false)],
            'name' => ['required', 'unique:roles,name,NULL,id,deleted_at,NULL',
                'not_in:' . implode(",",[Constants::ROLE_SUPER_ADMIN, Constants::ROLE_PARTNER, Constants::ROLE_PUBLIC])],
            'is_admin' => ['sometimes', new NotPresent],
            'guard_name' => ['sometimes', new NotPresent],
            'permission_ids' => ['required', 'array'],
            'permission_ids.*' => ['required', 'distinct' ,'exists:permissions,id']
        ];
    }
}
