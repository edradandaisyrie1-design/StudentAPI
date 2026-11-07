<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminPassword = env('SEED_ADMIN_PASSWORD');
        $userPassword = env('SEED_USER_PASSWORD');

        if (! $adminPassword || ! $userPassword) {
            $this->command->error('Set SEED_ADMIN_PASSWORD and SEED_USER_PASSWORD in your .env first.');

            return;
        }

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'API Administrator', 'password' => $adminPassword, 'role' => 'admin'],
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'Regular User', 'password' => $userPassword, 'role' => 'user'],
        );
    }
}