<?php

namespace App\Http\Requests\Member;

use App\Enums\Workspace\Capabilities;
use App\Enums\Workspace\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Spatie\Permission\PermissionRegistrar;

class InviteMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email'),
            ],
            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Please enter the member’s email address.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'A user with this email already exists.',
            'role.required' => 'Please select a role for the member.',
            'role.exists' => 'The selected role is invalid.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $user = $this->user();
                $role = $this->input('role');

                if (!$user->hasPermissionTo(Capabilities::MEMBER_INVITE->value)) {
                    $validator->errors()->add('email', 'You are not allowed to invite members.');
                }

                if ($role === RoleEnum::OWNER->value &&
                    !$user->hasPermissionTo(Capabilities::MEMBER_MAKE_OWNER->value)) {
                    $validator->errors()->add('role', 'You are not allowed to assign the Owner role.');
                }

                if ($role === RoleEnum::ADMIN->value &&
                    !$user->hasPermissionTo(Capabilities::MEMBER_MAKE_ADMIN->value)) {
                    $validator->errors()->add('role', 'You are not allowed to assign the Admin role.');
                }
            },
        ];
    }
}
