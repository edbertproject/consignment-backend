<?php

namespace Database\Seeders;

use App\Entities\Invoice;
use App\Entities\Order;
use App\Entities\Product;
use App\Entities\User;
use App\Services\InvoiceService;
use App\Services\NumberSettingService;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserAuctionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::beginTransaction();

            for ($u=1; $u <= 10; $u++) {
                $entity = User::create([
                    'username' => 'seller'. $u,
                    'name' => 'Test Seller ' . $u,
                    'email' => 'testseller' . $u . '@default.id',
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ]);

                $entity->syncRoles('Partner');

                $entity->partner()->create([
                    'full_address' => Str::random(),
                    'postal_code' => Str::random(5),
                    'province_id' => 1,
                    'city_id' => 1,
                    'district_id' => 1,
                    'status' => Constants::PARTNER_STATUS_APPROVED,
                ]);
            }

            for ($u=1; $u <= 25; $u++) {
                $entity = User::create([
                    'username' => 'buyer'.$u,
                    'name' => 'Test Buyer ' . $u,
                    'email' => 'testbuyer'.$u.'@default.id',
                    'password' => Hash::make('12345678'),
                    'is_active' => true
                ]);

                $entity->syncRoles('Public');
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
