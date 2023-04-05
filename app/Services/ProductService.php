<?php

namespace App\Services;

use App\Entities\Cart;
use App\Entities\Order;
use App\Entities\Product;
use App\Notifications\AuctionWinnerNotification;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;

class ProductService
{
    public static function checkActive() {
        return Product::query()
            ->where('status', Constants::PRODUCT_STATUS_APPROVED)
            ->whereNotNull('start_date')
            ->where('start_date','<=',Carbon::now()->tz('Asia/Jakarta'))
            ->update(['status' => Constants::PRODUCT_STATUS_ACTIVE]);
    }

    public static function checkClosed() {
        $products = Product::query()
            ->where('status', Constants::PRODUCT_STATUS_ACTIVE)
            ->whereNotNull('end_date')
            ->where('end_date','<',Carbon::now()->tz('Asia/Jakarta'))
            ->get();

        foreach ($products as $product) {
            if ($product->type === Constants::PRODUCT_TYPE_SPECIAL_AUCTION) {
                $redisIdentifier= 'product_bid:'.$product->id;
                $bids = collect(json_decode(Redis::get($redisIdentifier)));

                $lastBid = $bids->last();
                $product->winner_id = $lastBid->user_id;

                // $product->winner->notify(new AuctionWinnerNotification());
            }

            $product->status = Constants::PRODUCT_STATUS_CLOSED;
            $product->save();
        }
    }

    public static function updateAvailableQuantity($product, $availableQuantity) {

        $product->available_quantity = $availableQuantity;

        if ($availableQuantity === 0) {
            $product->status = Constants::PRODUCT_STATUS_SOLD;
        }

        $product->save();

        return true;
    }
}
