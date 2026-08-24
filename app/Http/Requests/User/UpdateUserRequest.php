<?php

namespace App\Http\Requests\User;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('user')) ?? false;
    }

    public function rules(): array
    {
        $targetUser = $this->route('user');

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($targetUser?->id),
            ],
            'password' => [
                'nullable',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
            'role_id' => [
                'nullable',
                Rule::exists('roles', 'id')->where(function ($query) {
                    return $query->where('agency_id', $this->user()->agency_id);
                }),
            ],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            $targetUser = $this->route('user');

            if (! $targetUser instanceof User || ! $targetUser->isOnlyAgencyAdmin()) {
                return;
            }

            $requestedRoleId = $this->input('role_id', $targetUser->role_id);
            $keepsAdminRole = Role::withoutGlobalScopes()
                ->whereKey($requestedRoleId)
                ->where('agency_id', $targetUser->agency_id)
                ->where('slug', 'admin')
                ->exists();

            if (! $keepsAdminRole) {
                $validator->errors()->add(
                    'role_id',
                    'The last agency administrator cannot be demoted.'
                );
            }
        }];
    }
}
