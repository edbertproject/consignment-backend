<?php

namespace Database\Seeders;

use App\Entities\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'username' => 'super_admin',
                'name' => 'Super Admin',
                'email' => 'super@admin.id',
                'password' => Hash::make('12345678'),
            ],
            [
                'username' => 'admin',
                'name' => 'Admin',
                'email' => 'default@admin.id',
                'password' => Hash::make('12345678'),
            ]
        ];

        foreach ($users as $user) {
            $entity = User::updateOrCreate($user);

            $entity->syncRoles($user['name']);
        }
    }
}
