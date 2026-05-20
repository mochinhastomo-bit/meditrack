<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'      => 'Super Admin',
            'email'     => 'admin@meditrack.com',
            'password'  => Hash::make('password'),
            'role'      => 'superadmin',
            'is_active' => true,
            'phone'     => '08123456789',
        ]);
    }
}
