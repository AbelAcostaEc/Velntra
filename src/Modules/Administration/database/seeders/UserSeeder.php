<?php

namespace Modules\Administration\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Modules\Administration\Models\User;
use RuntimeException;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $administrators = User::whereHas('roles', fn ($query) => $query->where('name', 'admin'))->get();

        if ($administrators->isNotEmpty()) {
            if (app()->environment('production') && $administrators->contains(
                fn (User $user) => Hash::check('admin@velntra.com', $user->password)
            )) {
                throw new RuntimeException('Cambia la contraseña inicial conocida del administrador antes de iniciar producción.');
            }

            return;
        }

        $email = (string) config('initial_admin.email');
        $password = (string) config('initial_admin.password');

        if (app()->environment('production') && (
            $email === 'admin@velntra.com'
            || $password === 'admin@velntra.com'
            || blank($email)
            || blank($password)
        )) {
            throw new RuntimeException('Configure INITIAL_ADMIN_EMAIL y una INITIAL_ADMIN_PASSWORD segura antes de iniciar producción.');
        }

        if (User::where('email', $email)->exists()) {
            throw new RuntimeException('El correo del administrador inicial ya pertenece a un usuario sin rol admin. Asígnale el rol manualmente.');
        }

        User::create([
            'email' => $email,
            'name' => 'Administrator',
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ])->assignRole('admin');
    }
}
