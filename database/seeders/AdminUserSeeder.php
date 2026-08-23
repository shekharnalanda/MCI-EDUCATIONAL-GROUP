<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'mcieducationalgroup@gmail.com');
        $password = env('ADMIN_PASSWORD');

        if (! $password) {
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'MCI Administrator'),
                'password' => Hash::make($password),
            ]
        );
    }
}
