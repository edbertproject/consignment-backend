<?php

namespace Database\Seeders;

use App\Entities\Role;
use App\Utils\Constants;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class RefreshPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('db:seed', ['--class' => 'PermissionSeeder']);

        $roles = Role::query()->whereIn('name',[
            Constants::ROLE_SUPER_ADMIN
        ])->get();

        foreach ($roles as $role) {
            $role->syncPermissions(Permission::pluck('name'));
        }

        $partnerRole = Role::query()->whereIn('name',[
            Constants::ROLE_PARTNER
        ])->first();

        $partnerRole->syncPermissions(
            Permission::query()
                ->whereIn('group', ['product','order'])
                ->pluck('name')
        );
    }
}
