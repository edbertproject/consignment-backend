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
                'label' => 'Product Category',
                'group' => 'product_category',
                'names' => Constants::PERMISSION_RWD
            ]
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
