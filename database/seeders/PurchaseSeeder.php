<?php

namespace Database\Seeders;

use App\Entities\Invoice;
use App\Entities\Order;
use App\Entities\OrderStatus;
use App\Entities\Product;
use App\Entities\User;
use App\Services\InvoiceService;
use App\Services\NumberSettingService;
use App\Services\OrderService;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PurchaseSeeder extends Seeder
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
            $prices = [2500000,5500000,18500000];
            $multipliedPrice = [50000,250000,150000];

            for ($u=201; $u <= 400; $u++) {
                $partner = User::query()->whereHas('roles', function ($query) {
                    $query->whereIn('role_id', [
                        Constants::ROLE_PARTNER_ID
                    ]);
                })->inRandomOrder()->first();

                $types = [
                    Constants::PRODUCT_TYPE_CONSIGN,
                    Constants::PRODUCT_TYPE_AUCTION,
                ];
                $type = $types[array_rand($types)];

                $product = [
                    'product_category_id' => 4,
                    'type' => $type,
                    'participant' => null,
                    'partner_id' => $partner->partner->id,
                    'name' => 'Tes produk ' . $u,
                    'slug' => Str::slug('Tes produk ' . $u . Str::random('4')),
                    'start_date' => Carbon::now()->startOfMonth()->addDays(rand(0, 30)),
                    'weight' => rand(0,5),
                    'quantity' => 1,
                    'available_quantity' => 1,
                    'long_dimension' => rand(5,10),
                    'wide_dimension' => rand(5,10),
                    'high_dimension' => rand(5,10),
                    'condition' => Constants::PRODUCT_CONDITIONS[array_rand(Constants::PRODUCT_CONDITIONS)],
                    'warranty' => Constants::PRODUCT_WARRANTIES[array_rand(Constants::PRODUCT_WARRANTIES)],
                    'description' => Str::random(30),
                    'status' => Constants::PRODUCT_STATUSES[array_rand(Constants::PRODUCT_STATUSES)]
                ];

                $product['end_date'] = $product['start_date']->addHours(rand(8,48));

                if ($type === Constants::PRODUCT_TYPE_CONSIGN) {
                    $product['price'] = $prices[array_rand($prices)];
                } else {
                    $product['start_price'] = $prices[array_rand($prices)];
                    $product['multiplied_price'] = $multipliedPrice[array_rand($multipliedPrice)];
                }

                $product = Product::create($product);

                if ($product->type !== Constants::PRODUCT_TYPE_CONSIGN) {
                    $beforeAmount = $product->start_price;
                    for ($p=1; $p <= rand(25,30); $p++) {
                        $bidder = User::query()->whereHas('roles', function ($query) {
                            $query->whereIn('role_id', [
                                Constants::ROLE_PUBLIC_ID,
                                Constants::ROLE_PARTNER_ID,
                            ]);
                        })->inRandomOrder()->first();

                        $product->bids()->create([
                            'amount' => $beforeAmount += $product->multiplied_price,
                            'user_id' => $bidder->id,
                            'date_time' => Carbon::parse($product->start_date)->addMinutes($p*2)
                        ]);
                    }
                }

                if ($product->status === Constants::PRODUCT_STATUS_SOLD) {
                    $buyer = User::query()->whereHas('roles', function ($query) {
                        $query->whereIn('role_id', [
                            Constants::ROLE_PUBLIC_ID,
                            Constants::ROLE_PARTNER_ID,
                        ]);
                    })->inRandomOrder()->first();

                    $invoice = Invoice::query()
                        ->create([
                            'user_id' => $buyer->id,
                            'date' => Carbon::parse($product->end_date)->addDay(),
                            'number' => NumberSettingService::generate(Invoice::class),
                            'expires_at' => Carbon::parse($product->end_date)->addDay()->addMinutes(Constants::INVOICE_EXPIRES),
                            'status' => Constants::INVOICE_STATUS_PAID,
                            'payment_method_id' => 1,
                            'courier_code' => 'JNE',
                            'courier_service' => 'OKE',
                            'courier_esd' => '1-2 hari',
                            'subtotal' => 0,
                            'tax_amount' => 0,
                            'admin_fee' => 0,
                            'platform_fee' => 0,
                            'courier_cost' => 200000,
                            'grand_total' => 0,
                        ]);

                    $ordered = Order::query()
                        ->create([
                            'date' => Carbon::parse($product->end_date)->addDay(),
                            'number' => NumberSettingService::generate(Order::class),
                            'invoice_id' => $invoice->id,
                            'user_id' => $buyer->id,
                            'user_address_id' => 1,
                            'product_id' => $product->id,
                            'partner_id' => $product->partner_id,
                            'quantity' => $product->quantity,
                            'price' => $product->type === Constants::PRODUCT_TYPE_CONSIGN ? $product->price : $product->start_price,
                        ]);

                    $primaryStatus = [
                        Constants::ORDER_STATUS_WAITING_PAYMENT,
                        Constants::ORDER_STATUS_PAID,
                        Constants::ORDER_STATUS_PROCESS,
                        Constants::ORDER_STATUS_FINISH,
                    ];
                    foreach ($primaryStatus as $status) {
                        OrderService::updateStatus($ordered->id, $status);
                    }

                    $seller = [
                        Constants::ORDER_SELLER_STATUS_WAITING_CONFIRM,
                        Constants::ORDER_SELLER_STATUS_PROCESSING,
                        Constants::ORDER_SELLER_STATUS_ON_DELIVERY,
                        Constants::ORDER_SELLER_STATUS_ARRIVED,
                        Constants::ORDER_SELLER_STATUS_COMPLETE,
                    ];
                    foreach ($seller as $status) {
                        OrderService::updateStatus($ordered->id, $status, Constants::ORDER_STATUS_TYPE_SELLER);
                    }

                    $buyer = [
                        Constants::ORDER_BUYER_STATUS_PENDING,
                        Constants::ORDER_BUYER_STATUS_PROCESSED,
                        Constants::ORDER_BUYER_STATUS_ON_DELIVERY,
                        Constants::ORDER_BUYER_STATUS_ARRIVED,
                        Constants::ORDER_BUYER_STATUS_COMPLETE,
                    ];
                    foreach ($buyer as $status) {
                        OrderService::updateStatus($ordered->id, $status, Constants::ORDER_STATUS_TYPE_BUYER);
                    }

                    $invoice->subtotal = $ordered->price * $ordered->quantity;
                    $invoice->admin_fee = 2500;
                    $invoice->platform_fee = (int) (($invoice->subtotal * Constants::INVOICE_FEE_PLATFORM_AMOUNT_PERCENTAGE) / 100);
                    $invoice->grand_total = $invoice->subtotal + $invoice->tax_amount + $invoice->admin_fee + $invoice->platform_fee + $invoice->courier_cost;
                    $invoice->save();
                }
            }


            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
