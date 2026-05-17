<?php
namespace App\Services;

use App\Models\User;

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
        }

        $user->update($data);

        return $user;
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
