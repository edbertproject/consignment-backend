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
                'is_active' => true
            ],
            [
                'username' => 'admin',
                'name' => 'Admin',
                'email' => 'admin@default.id',
                'password' => Hash::make('12345678'),
                'is_active' => true
            ],
            [
                'username' => 'partner',
                'name' => 'Partner',
                'email' => 'partner@default.id',
                'password' => Hash::make('12345678'),
                'is_active' => true
            ],
            [
                'username' => 'public',
                'name' => 'Public',
                'email' => 'public@default.id',
                'password' => Hash::make('12345678'),
                'is_active' => true
            ]
        ];

        foreach ($users as $user) {
            $entity = User::updateOrCreate(['username' => $user['username']], $user);

            $entity->syncRoles($user['name']);
        }
    }
}
