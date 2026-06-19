<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Http\Requests\Account\UpdateSettingsRequest;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{
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

        if ($request->boolean('remove_avatar') && $user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $data['avatar_path'] = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $data['avatar_path'] = $request->file('avatar')->store("agencies/{$user->agency_id}/avatars", 'public');
        }

        unset($data['avatar'], $data['remove_avatar']);

        $user->update($data);

        return back()->with('success', 'Profile updated');
    }

    public function editSettings()
    {
        $user = request()->user()->loadMissing(['role', 'agency']);

        return view('account.settings', [
            'user' => $user,
            'preferences' => $this->preferences($user),
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

    private function preferences($user): array
    {
        return array_merge([
            'email_notifications' => true,
            'product_updates' => true,
            'security_alerts' => true,
        ], $user->profile_preferences ?? []);
    }
}
