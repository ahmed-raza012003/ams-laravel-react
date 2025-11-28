<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'createdAt' => now(), 'updatedAt' => now()],
            ['name' => 'customer', 'createdAt' => now(), 'updatedAt' => now()],
        ];

        foreach ($roles as $role) {
            DB::table('Role')->updateOrInsert(
                ['name' => $role['name']],
                $role
            );
        }
    }
}
