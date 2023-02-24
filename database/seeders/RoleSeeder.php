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
            ['code' => 'partner', 'name' => 'Partner', 'guard_name' => 'api'],
            ['code' => 'public', 'name' => 'Public', 'guard_name' => 'api'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['code' => $role['code']], $role);
        }
    }
}
