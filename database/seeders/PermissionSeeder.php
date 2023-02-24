<?php

namespace Database\Seeders;

use App\Utils\Constants;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            [
                'label' => 'User Public',
                'group' => 'user_public',
                'names' => Constants::PERMISSION_RW
            ],
            [
                'label' => 'User Internal',
                'group' => 'user_internal',
                'names' => Constants::PERMISSION_RWD
            ],
            [
                'label' => 'User Partner',
                'group' => 'user_partner',
                'names' => Constants::PERMISSION_RWD
            ],
            [
                'label' => 'Role',
                'group' => 'role',
                'names' => Constants::PERMISSION_RWD
            ],
            [
                'label' => 'Product Category',
                'group' => 'product_category',
                'names' => Constants::PERMISSION_RWD
            ],
        ];



        foreach ($permissions as $permission) {
            foreach ($permission['names'] as $name) {
                $name = $name . ' ' . strtolower($permission['label']);
                Permission::query()->updateOrCreate(
                    ['name' => $name],
                    array_merge(
                        [
                            'name' => $name
                        ],
                        array_diff_key(
                            $permission,
                            array_flip(['names'])
                        )
                    )
                );
            }
        }
    }
}
