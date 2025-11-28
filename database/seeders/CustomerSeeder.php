<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\PrismaService;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customerRole = PrismaService::getRoleByName('customer');

        if (!$customerRole) {
            $customerRoleId = DB::table('Roles')->insertGetId([
                'name' => 'customer',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $customerRoleId = $customerRole->id;
        }

        $existingCustomer = DB::table('Users')->where('email', 'customer@financeflow.com')->first();

        if (!$existingCustomer) {
            $userId = DB::table('Users')->insertGetId([
                'name' => 'Test Customer',
                'email' => 'customer@financeflow.com',
                'password' => Hash::make('password123'),
                'role_id' => $customerRoleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Create Customer record linked to the user
            PrismaService::createCustomer([
                'userId' => $userId,
                'name' => 'Test Customer',
                'email' => 'customer@financeflow.com',
            ]);
        }
    }
}

