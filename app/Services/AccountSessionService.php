<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountSessionService
{
    public function supportsManagement(): bool
    {
        return config('session.driver') === 'database';
    }

    public function sessions(User $user, Request $request): array
    {
        if (! $this->supportsManagement()) {
            return [[
                'fingerprint' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $this->userAgent((string) $request->userAgent()),
                'last_activity' => now(),
                'is_current' => true,
            ]];
        }

        $currentId = $request->session()->getId();

        return DB::table($this->table())
            ->where('user_id', $user->getAuthIdentifier())
            ->orderByDesc('last_activity')
            ->get()
            ->map(fn ($session) => [
                'fingerprint' => $this->fingerprint((string) $session->id),
                'ip_address' => $session->ip_address ?: 'Unknown IP',
                'user_agent' => $this->userAgent((string) $session->user_agent),
                'last_activity' => Carbon::createFromTimestamp((int) $session->last_activity),
                'is_current' => hash_equals((string) $session->id, $currentId),
            ])
            ->all();
    }

    public function count(User $user, Request $request): int
    {
        return count($this->sessions($user, $request));
    }

    public function revoke(User $user, string $fingerprint, string $currentId): bool
    {
        if (! $this->supportsManagement()) {
            return false;
        }

        $session = DB::table($this->table())
            ->where('user_id', $user->getAuthIdentifier())
            ->get(['id'])
            ->first(fn ($row) => hash_equals(
                $this->fingerprint((string) $row->id),
                $fingerprint
            ));

        if ($session === null || hash_equals((string) $session->id, $currentId)) {
            return false;
        }

        return DB::table($this->table())
            ->where('id', $session->id)
            ->where('user_id', $user->getAuthIdentifier())
            ->delete() === 1;
    }

    public function revokeOthers(User $user, string $currentId): int
    {
        if (! $this->supportsManagement()) {
            return 0;
        }

        $currentBelongsToUser = DB::table($this->table())
            ->where('id', $currentId)
            ->where('user_id', $user->getAuthIdentifier())
            ->exists();

        if (! $currentBelongsToUser) {
            return 0;
        }

        return DB::table($this->table())
            ->where('user_id', $user->getAuthIdentifier())
            ->where('id', '!=', $currentId)
            ->delete();
    }

    public function fingerprint(string $sessionId): string
    {
        return hash_hmac('sha256', $sessionId, (string) config('app.key'));
    }

    private function table(): string
    {
        return (string) config('session.table', 'sessions');
    }

    private function userAgent(string $userAgent): string
    {
        return $userAgent === '' ? 'Unknown device' : Str::limit($userAgent, 120);
    }
}
