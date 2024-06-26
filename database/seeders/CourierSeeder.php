<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $couriers = [
            [
                'code' => 'jne',
                'name' => 'JNE',
            ],
            [
                'code' => 'pos',
                'name' => 'POS',
            ],
            [
                'code' => 'tiki',
                'name' => 'TIKI',
            ]
        ];
    }
}
