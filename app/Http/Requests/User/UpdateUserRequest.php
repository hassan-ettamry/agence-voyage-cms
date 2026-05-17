<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name'     => 'required|string|max:255',
            'email'    => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($targetUser?->id),
            ],
            'password' => 'nullable|min:6',
            'role_id'  => [
                'nullable',
                Rule::exists('roles', 'id')->where(function ($query) {
                    return $query->where('agency_id', $this->user()->agency_id);
                }),
            ],
        ];
    }
}
