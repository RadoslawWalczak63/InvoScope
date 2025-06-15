<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = app()->isProduction() ? Str::random(16) : 'test';

        $user = User::firstOrCreate(
            ['email' => 'radoslawwalczak63@gmail.com'],
            [
                'name' => 'Radosław Walczak',
                'password' => bcrypt($password),
                'email_verified_at' => now(),
            ]);

        if ($user->wasRecentlyCreated) {
            $this->command->info("User created: {$user->email} with password: {$password}");
        } else {
            $this->command->info("User already exists: {$user->email}");
        }
    }
}
