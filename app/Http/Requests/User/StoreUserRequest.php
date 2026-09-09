<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', User::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
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
}
