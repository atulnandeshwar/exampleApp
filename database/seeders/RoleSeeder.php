<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         $roles = [
             [
                'name'         => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'         => 'vendor',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'         => 'customer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
