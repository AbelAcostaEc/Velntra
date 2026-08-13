<?php

namespace Modules\Administration\Services;

use Illuminate\Support\Facades\DB;
use Modules\Administration\Models\User;

class UserService
{
    /**
     * Find a user by ID or fail.
     */
    public function find(int $id): User
    {
        return User::findOrFail($id);
    }

    /**
     * Create a new user and sync roles.
     */
    public function create(array $data, array $roles = []): User
    {
        return DB::transaction(function () use ($data, $roles) {
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => $data['password'],
            ]);

            if (!empty($roles)) {
                $user->syncRoles($roles);
            }

            return $user;
        });
    }

    /**
     * Update an existing user and sync roles.
     */
    public function update(User $user, array $data, array $roles = []): User
    {
        return DB::transaction(function () use ($user, $data, $roles) {
            $updateData = [
                'name'  => $data['name'],
                'email' => $data['email'],
            ];

            if (!empty($data['password'])) {
                $updateData['password'] = $data['password'];
            }

            $user->update($updateData);

            $user->syncRoles($roles);

            return $user;
        });
    }

    /**
     * Delete a user.
     */
    public function delete(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            return (bool) $user->delete();
        });
    }
}
