<?php

namespace Database\Seeders;

use App\Entities\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        User::updateOrCreate([
            'name' => 'Super Admin',
            'email' => 'admin@ecommerce.id',
            'password' => Hash::make('loremipsum'),
        ]);
    }
}
