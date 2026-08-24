<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SecurityEventLogger
{
    private const SENSITIVE_KEYS = [
        'password',
        'token',
        'authorization',
        'cookie',
        'session',
        'secret',
    ];

    public function record(
        string $event,
        ?User $user = null,
        ?Request $request = null,
        array $context = []
    ): void {
        $request ??= app()->bound('request') ? request() : null;
        $user ??= $request?->user();

        $baseContext = [
            'user_id' => $user?->getAuthIdentifier(),
            'agency_id' => $user?->agency_id,
            'ip' => $request?->ip(),
            'method' => $request?->method(),
            'path' => $request?->path(),
            'request_id' => $request?->attributes->get('request_id'),
            'user_agent' => Str::limit((string) $request?->userAgent(), 255, ''),
        ];

        try {
            Log::channel('security')->info(
                Str::limit($event, 120, ''),
                $this->sanitize(array_merge($baseContext, $context))
            );
        } catch (Throwable) {
            // Security logging must never make authentication unavailable.
        }
    }

    public function emailFingerprint(?string $email): string
    {
        return hash('sha256', Str::lower(trim((string) $email)));
    }

    private function sanitize(array $context): array
    {
        $sanitized = [];

        foreach ($context as $key => $value) {
            $normalizedKey = Str::lower((string) $key);
            $isSensitive = collect(self::SENSITIVE_KEYS)
                ->contains(fn (string $sensitive) => str_contains($normalizedKey, $sensitive));

            if ($isSensitive) {
                $sanitized[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitize($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = Str::limit($value, 500, '');
            } elseif (is_scalar($value) || $value === null) {
                $sanitized[$key] = $value;
            } else {
                $sanitized[$key] = get_debug_type($value);
            }
        }

        return $sanitized;
    }
}
