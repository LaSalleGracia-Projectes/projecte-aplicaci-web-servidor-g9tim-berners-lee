<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@critflix.com',
            'password' => Hash::make('admin123'),
            'rol' => 'admin',
            'email_verified_at' => now()
        ]);
    }
}
