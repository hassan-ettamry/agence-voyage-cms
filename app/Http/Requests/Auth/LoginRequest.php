<?php

namespace App\Http\Requests\Auth;

use App\Services\SecurityEventLogger;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Autorisation
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }

    /**
     * Messages personnalisés
     */
    public function messages(): array
    {
        return [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ];
    }

    /**
     * Authentifier l'utilisateur
     */
    public function authenticate(): void
    {
        if (! auth()->attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            $logger = app(SecurityEventLogger::class);
            $logger->record('auth.login_failed', null, $this, [
                'email_hash' => $logger->emailFingerprint($this->input('email')),
            ]);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
    }

    /**
     * Préparation des données
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim($this->email)),
        ]);
    }
}
