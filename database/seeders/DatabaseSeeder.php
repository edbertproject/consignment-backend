<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Entities\Product;
use App\Utils\Constants;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $product =  Product::query()->orderBy('created_at','desc')
            ->first()->toArray();

        foreach (Constants::PRODUCT_STATUSES as $status) {
            Product::create(array_merge($product, [
                'status' => $status
            ]));
        }
    }
}
