<?php

namespace Database\Seeders;

use App\Entities\Role;
use Illuminate\Database\Seeder;

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
            ['code' => 'super_admin', 'name' => 'Super Admin', 'is_admin' => true, 'guard_name' => 'api'],
            ['code' => 'admin', 'name' => 'Admin', 'guard_name' => 'api'],
        ];

        foreach ($roles as $index => $role) {
            Role::updateOrCreate(['code' => $role['code']], $role);
        }
    }
}
