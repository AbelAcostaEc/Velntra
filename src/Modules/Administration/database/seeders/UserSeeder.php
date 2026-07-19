<?php

namespace Modules\Administration\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Administration\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@velntra.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin@velntra.com'),
                'email_verified_at' => now(),
            ]
        );

        $admin->assignRole('admin');
    }
}
