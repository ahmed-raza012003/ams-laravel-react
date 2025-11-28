<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = DB::table('Role')->where('name', 'admin')->first();

        if (!$adminRole) {
            $adminRoleId = DB::table('Role')->insertGetId([
                'name' => 'admin',
                'createdAt' => now(),
                'updatedAt' => now(),
            ]);
        } else {
            $adminRoleId = $adminRole->id;
        }

        $existingAdmin = DB::table('User')->where('email', 'admin@financeflow.com')->first();

        if (!$existingAdmin) {
            DB::table('User')->insert([
                'name' => 'Admin User',
                'email' => 'admin@financeflow.com',
                'password' => Hash::make('password123'),
                'roleId' => $adminRoleId,
                'createdAt' => now(),
                'updatedAt' => now(),
            ]);
        }
    }
}
