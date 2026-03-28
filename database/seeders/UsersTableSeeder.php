<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    // use WithoutModelEvents;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = [
            'name' => 'Danilo',
            'email' => 'danilobautista2004@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt('cure100'),
            'remember_token' => \Illuminate\Support\Str::random(10)
        ];

        User::updateOrCreate(['email' => $user['email']], $user);
    }
}
