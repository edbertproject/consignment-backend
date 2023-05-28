<?php

namespace App\Services;

use App\Entities\Cart;
use App\Entities\Order;
use App\Entities\Product;
use App\Notifications\AuctionWinnerNotification;
use App\Notifications\SpecialAuctionParticipantNotification;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

                $product->winner->notify(new AuctionWinnerNotification($product));
            }

            $product->status = Constants::PRODUCT_STATUS_CLOSED;
            $product->save();
        }
    }

    public static function informSpecialAuctionParticipant() {
        $products = Product::query()
            ->where('type',Constants::PRODUCT_TYPE_SPECIAL_AUCTION)
            ->where('status', Constants::PRODUCT_STATUS_APPROVED)
            ->where('start_date','<',Carbon::now()->subDays()->tz('Asia/Jakarta'))
            ->get();

        foreach ($products as $product) {
            foreach ($product->participants as $participant) {
                $participant->notify(new SpecialAuctionParticipantNotification($product));
            }
        }
    }

    public static function generateFrontendUrl($product) {
        return config('app.front_url') . '/products/' . $product->slug;
    }

    public static function updateAvailableQuantity($product, $availableQuantity) {

        $product->available_quantity = $availableQuantity;

        if ($availableQuantity === 0) {
            $product->status = Constants::PRODUCT_STATUS_SOLD;
        }

        $product->save();

        return true;
    }

    public static function determineParticipantAuction($participantNumber) {
        $participants = DB::table('users')
            ->join('model_has_roles','model_has_roles.model_id','users.id')
            ->join('roles','roles.id','model_has_roles.role_id')
            ->select('users.id',
                'temp_a.auction_count',
                'temp_b.bid_accumulation',
                'temp_c.purchase_count',
                'temp_d.purchase_accumulation',
                'temp_e.sales_count',
                'temp_f.sales_accumulation',
            )->crossJoin(DB::raw('LATERAL (
                SELECT CAST(IFNULL(COUNT(DISTINCT product_bids.product_id),0) AS DECIMAL) AS auction_count
                FROM product_bids
                WHERE product_bids.user_id = users.id
            ) AS temp_a'))
            ->crossJoin(DB::raw('LATERAL (
                SELECT CAST(IFNULL(SUM(inner_temp_b.max_auction_amount),0) AS DECIMAL) AS bid_accumulation
                FROM (
                    SELECT IFNULL(MAX(product_bids.amount),1) AS max_auction_amount
                    FROM product_bids
                    WHERE product_bids.user_id = users.id
                    GROUP BY product_bids.product_id
                ) AS inner_temp_b
            ) AS temp_b'))
            ->crossJoin(DB::raw('LATERAL (
                SELECT CAST(IFNULL(COUNT(DISTINCT orders.id),0) AS DECIMAL) AS purchase_count
                FROM orders
                WHERE orders.user_id = users.id
                AND orders.status = "'. Constants::ORDER_STATUS_FINISH .'"
            ) AS temp_c'))
            ->crossJoin(DB::raw('LATERAL (
                SELECT CAST(IFNULL(SUM(invoices.grand_total),0) AS DECIMAL) AS purchase_accumulation
                FROM orders
                JOIN invoices ON invoices.id = orders.invoice_id
                WHERE orders.user_id = users.id
                AND orders.status = "'. Constants::ORDER_STATUS_FINISH .'"
            ) AS temp_d'))
            ->crossJoin(DB::raw('LATERAL (
                SELECT CAST(IFNULL(COUNT(DISTINCT orders.id),0) AS DECIMAL) AS sales_count
                FROM orders
                WHERE orders.partner_id = users.id
                AND orders.status = "'. Constants::ORDER_STATUS_FINISH .'"
            ) AS temp_e'))
            ->crossJoin(DB::raw('LATERAL (
                SELECT CAST(IFNULL(SUM(invoices.grand_total),0) AS DECIMAL) AS sales_accumulation
                FROM orders
                JOIN invoices ON invoices.id = orders.invoice_id
                WHERE orders.partner_id = users.id
                AND orders.status = "'. Constants::ORDER_STATUS_FINISH .'"
            ) AS temp_f'))
            ->whereRaw('users.id IN(
                SELECT DISTINCT model_has_roles.model_id
                FROM roles
                JOIN model_has_roles ON model_has_roles.role_id = roles.id
                WHERE roles.id IN('.implode(",",[Constants::ROLE_PARTNER_ID,Constants::ROLE_PUBLIC_ID]).')
            )')->get();

        if ($participantNumber > $participants->count()) {
            throw new \Exception("Total Participant is not enough");
        }

        // Weight Criteria
        $weights = [
            'auction_count' => 0.4,
            'bid_accumulation' => 0.3,
            'purchase_count' => 0.2,
            'purchase_accumulation' => 0.2,
            'sales_count' => 0.1,
            'sales_accumulation' => 0.1
        ];

        // Normalization
        $maxValues = [];
        foreach (array_keys(get_object_vars($participants->first())) as $column) {
            if (!in_array($column,['id','name'])) {
                $maxValues[$column] = $participants->max($column);
            }
        }

        $normData = [];
        foreach ($participants as $participant) {
            $temp = [];
            foreach (array_keys(get_object_vars($participant)) as $column) {
                if (!in_array($column,['id','name'])) {
                    $temp[$column] = $participant->$column / $maxValues[$column];
                } else {
                    $temp[$column] = $participant->$column;
                }
            }

            $normData[] = $temp;
        }

        // Calculation
        $calculations = [];
        foreach ($normData as $data) {
            $temp = [];

            foreach (array_keys($data) as $column) {
                if (!in_array($column,['id','name'])) {
                    $temp[] = pow($data[$column], $weights[$column]);
                }
            }

            $calculations[] = [
                'id' => $data['id'],
                'value' => array_sum($temp)
            ];
        }

        // Result normalization
        $sumCalculation = array_sum(array_column($calculations,'value'));
        $results = [];

        foreach ($calculations as $calculation) {
            $results[] = [
                'id' => $calculation['id'],
                'value' => $calculation['value'] / $sumCalculation
            ];
        }

        // Sorting desc
        usort($results, function ($a, $b) {
            return strcmp($a["value"], $b["value"]) * -1;
        });

        return array_slice($results,0,$participantNumber);
    }
}
