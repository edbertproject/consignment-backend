<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            DB::beginTransaction();
            foreach (DB::table('orders')->get() as $order) {
                \App\Services\OrderService::updateStatus($order->id,$order->status);
                \App\Services\OrderService::updateStatus($order->id,$order->status_seller,\App\Utils\Constants::ORDER_STATUS_TYPE_SELLER);
                \App\Services\OrderService::updateStatus($order->id,$order->status_buyer,\App\Utils\Constants::ORDER_STATUS_TYPE_BUYER);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'status_seller',
                'status_buyer',
                'status_seller_updated_at',
                'status_buyer_updated_at'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
