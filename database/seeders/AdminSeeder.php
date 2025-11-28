<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = DB::table('Roles')->where('name', 'admin')->first();

        if (!$adminRole) {
            $adminRoleId = DB::table('Roles')->insertGetId([
                'name' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $adminRoleId = $adminRole->id;
        }

        $existingAdmin = DB::table('Users')->where('email', 'admin@financeflow.com')->first();

        if (!$existingAdmin) {
            DB::table('Users')->insert([
                'name' => 'Admin User',
                'email' => 'admin@financeflow.com',
                'password' => Hash::make('password123'),
                'role_id' => $adminRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
