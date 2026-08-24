<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdateEmailRequest;
use App\Http\Requests\Account\UpdatePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Http\Requests\Account\UpdateSettingsRequest;
use App\Services\AccountSessionService;
use App\Services\SecureImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class AccountController extends Controller
{
    public function __construct(
        private AccountSessionService $sessionService,
        private SecureImageUploadService $imageUpload
    ) {}

    public function editProfile()
    {
        $user = request()->user()->loadMissing(['role', 'agency']);

        return view('account.profile', [
            'user' => $user,
            'preferences' => $this->preferences($user),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();
        $oldAvatar = $user->avatar_path;
        $storedAvatar = null;

        if ($request->hasFile('avatar')) {
            $storedAvatar = $this->imageUpload->store(
                $request->file('avatar'),
                "agencies/{$user->agency_id}/avatars",
                'avatar'
            );
            $data['avatar_path'] = $storedAvatar['path'];
        } elseif ($request->boolean('remove_avatar')) {
            $data['avatar_path'] = null;
        }

        unset($data['avatar'], $data['remove_avatar']);

        try {
            $user->update($data);
        } catch (Throwable $exception) {
            $this->imageUpload->delete($storedAvatar['path'] ?? null);

            throw $exception;
        }

        if ($oldAvatar !== $user->avatar_path) {
            $this->imageUpload->delete($oldAvatar);
        }

        return back()->with('success', 'Profile updated');
    }

    public function editSettings()
    {
        $user = request()->user()->loadMissing(['role', 'agency']);

        return view('account.settings', [
            'user' => $user,
            'preferences' => $this->preferences($user),
            'activeSessionCount' => $this->sessionService->count($user, request()),
            'sessionManagementAvailable' => $this->sessionService->supportsManagement(),
        ]);
    }

    public function updateSettings(UpdateSettingsRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();
        $preferences = $this->preferences($user);

        $preferences['email_notifications'] = $request->boolean('email_notifications');
        $preferences['product_updates'] = $request->boolean('product_updates');
        $preferences['security_alerts'] = $request->boolean('security_alerts');

        $user->update([
            'language' => $validated['language'],
            'timezone' => $validated['timezone'],
            'date_format' => $validated['date_format'],
            'time_format' => $validated['time_format'],
            'profile_preferences' => $preferences,
        ]);

        return back()->with('success', 'Account settings updated');
    }

    public function editPassword()
    {
        return view('account.security.password', ['user' => request()->user()]);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $request->user()->forceFill([
            'password' => $request->validated('password'),
            'remember_token' => Str::random(60),
            'password_changed_at' => now(),
        ])->save();

        $this->sessionService->revokeOthers($request->user(), $request->session()->getId());

        return redirect()
            ->route('account.settings.edit')
            ->with('success', 'Password updated and other sessions signed out.');
    }

    public function editEmail()
    {
        return view('account.security.email', ['user' => request()->user()]);
    }

    public function updateEmail(UpdateEmailRequest $request)
    {
        $email = $request->validated('email');

        if ($email === $request->user()->email) {
            return back()->with('success', 'Your email address is already up to date.');
        }

        $request->user()->forceFill([
            'email' => $email,
            'email_verified_at' => null,
        ])->save();
        $request->user()->sendEmailVerificationNotification();

        return redirect()
            ->route('verification.notice')
            ->with('status', 'verification-link-sent');
    }

    public function sessions(Request $request)
    {
        return view('account.security.sessions', [
            'sessions' => $this->sessionService->sessions($request->user(), $request),
            'managementAvailable' => $this->sessionService->supportsManagement(),
        ]);
    }

    public function destroySession(Request $request, string $session)
    {
        abort_unless(
            $this->sessionService->revoke(
                $request->user(),
                $session,
                $request->session()->getId()
            ),
            404
        );

        return back()->with('success', 'Session signed out.');
    }

    public function destroyOtherSessions(Request $request)
    {
        $count = $this->sessionService->revokeOthers(
            $request->user(),
            $request->session()->getId()
        );

        return back()->with('success', "{$count} other session(s) signed out.");
    }

    private function preferences($user): array
    {
        return array_merge([
            'email_notifications' => true,
            'product_updates' => true,
            'security_alerts' => true,
        ], $user->profile_preferences ?? []);
    }
}
