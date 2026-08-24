<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Str;

class UserService
{
    public function create(array $data, User $creator): User
    {
        $data['agency_id'] = $creator->agency_id;

        // password auto hashed via model mutator
        return User::create($data);
    }

    public function update(User $user, array $data): User
    {
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password_changed_at'] = now();
            $data['remember_token'] = Str::random(60);
        }

        $user->update($data);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
