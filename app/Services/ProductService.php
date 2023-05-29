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
//        $participants = DB::table('users')
//            ->join('model_has_roles','model_has_roles.model_id','users.id')
//            ->join('roles','roles.id','model_has_roles.role_id')
//            ->select('users.id',
//                'temp_a.auction_count',
//                'temp_b.bid_accumulation',
//                'temp_c.purchase_count',
//                'temp_d.purchase_accumulation',
//                'temp_e.sales_count',
//                'temp_f.sales_accumulation',
//            )->crossJoin(DB::raw('LATERAL (
//                SELECT CAST(IFNULL(COUNT(DISTINCT product_bids.product_id),0) AS DECIMAL) AS auction_count
//                FROM product_bids
//                WHERE product_bids.user_id = users.id
//            ) AS temp_a'))
//            ->crossJoin(DB::raw('LATERAL (
//                SELECT CAST(IFNULL(SUM(inner_temp_b.max_auction_amount),0) AS DECIMAL) AS bid_accumulation
//                FROM (
//                    SELECT IFNULL(MAX(product_bids.amount),1) AS max_auction_amount
//                    FROM product_bids
//                    WHERE product_bids.user_id = users.id
//                    GROUP BY product_bids.product_id
//                ) AS inner_temp_b
//            ) AS temp_b'))
//            ->crossJoin(DB::raw('LATERAL (
//                SELECT CAST(IFNULL(COUNT(DISTINCT orders.id),0) AS DECIMAL) AS purchase_count
//                FROM orders
//                WHERE orders.user_id = users.id
//                AND orders.status = "'. Constants::ORDER_STATUS_FINISH .'"
//            ) AS temp_c'))
//            ->crossJoin(DB::raw('LATERAL (
//                SELECT CAST(IFNULL(SUM(invoices.grand_total),0) AS DECIMAL) AS purchase_accumulation
//                FROM orders
//                JOIN invoices ON invoices.id = orders.invoice_id
//                WHERE orders.user_id = users.id
//                AND orders.status = "'. Constants::ORDER_STATUS_FINISH .'"
//            ) AS temp_d'))
//            ->crossJoin(DB::raw('LATERAL (
//                SELECT CAST(IFNULL(COUNT(DISTINCT orders.id),0) AS DECIMAL) AS sales_count
//                FROM orders
//                WHERE orders.partner_id = users.id
//                AND orders.status = "'. Constants::ORDER_STATUS_FINISH .'"
//            ) AS temp_e'))
//            ->crossJoin(DB::raw('LATERAL (
//                SELECT CAST(IFNULL(SUM(invoices.grand_total),0) AS DECIMAL) AS sales_accumulation
//                FROM orders
//                JOIN invoices ON invoices.id = orders.invoice_id
//                WHERE orders.partner_id = users.id
//                AND orders.status = "'. Constants::ORDER_STATUS_FINISH .'"
//            ) AS temp_f'))
//            ->whereRaw('users.id IN(
//                SELECT DISTINCT model_has_roles.model_id
//                FROM roles
//                JOIN model_has_roles ON model_has_roles.role_id = roles.id
//                WHERE roles.id IN('.implode(",",[Constants::ROLE_PARTNER_ID,Constants::ROLE_PUBLIC_ID]).')
//            )')->get();

        $participants = collect(array (
            0 =>
                array (
                    'id' => '79',
                    'name' => 'User 1',
                    'auction_count' => '68',
                    'bid_accumulation' => '781600000',
                    'purchase_count' => '15',
                    'purchase_accumulation' => '78300000',
                    'sales_count' => '14',
                    'sales_accumulation' => '35700000',
                ),
            1 =>
                array (
                    'id' => '80',
                    'name' => 'User 2',
                    'auction_count' => '64',
                    'bid_accumulation' => '828550000',
                    'purchase_count' => '2',
                    'purchase_accumulation' => '92100000',
                    'sales_count' => '3',
                    'sales_accumulation' => '12200000',
                ),
            2 =>
                array (
                    'id' => '81',
                    'name' => 'User 3',
                    'auction_count' => '62',
                    'bid_accumulation' => '686150000',
                    'purchase_count' => '12',
                    'purchase_accumulation' => '70700000',
                    'sales_count' => '18',
                    'sales_accumulation' => '51700000',
                ),
            3 =>
                array (
                    'id' => '82',
                    'name' => 'User 4',
                    'auction_count' => '65',
                    'bid_accumulation' => '794850000',
                    'purchase_count' => '13',
                    'purchase_accumulation' => '24700000',
                    'sales_count' => '21',
                    'sales_accumulation' => '39700000',
                ),
            4 =>
                array (
                    'id' => '83',
                    'name' => 'User 5',
                    'auction_count' => '67',
                    'bid_accumulation' => '795800000',
                    'purchase_count' => '15',
                    'purchase_accumulation' => '76700000',
                    'sales_count' => '29',
                    'sales_accumulation' => '62200000',
                ),
            5 =>
                array (
                    'id' => '84',
                    'name' => 'User 6',
                    'auction_count' => '63',
                    'bid_accumulation' => '777550000',
                    'purchase_count' => '11',
                    'purchase_accumulation' => '43600000',
                    'sales_count' => '23',
                    'sales_accumulation' => '51800000',
                ),
            6 =>
                array (
                    'id' => '85',
                    'name' => 'User 7',
                    'auction_count' => '67',
                    'bid_accumulation' => '802650000',
                    'purchase_count' => '3',
                    'purchase_accumulation' => '78500000',
                    'sales_count' => '19',
                    'sales_accumulation' => '5300000',
                ),
            7 =>
                array (
                    'id' => '86',
                    'name' => 'User 8',
                    'auction_count' => '57',
                    'bid_accumulation' => '592900000',
                    'purchase_count' => '11',
                    'purchase_accumulation' => '97900000',
                    'sales_count' => '27',
                    'sales_accumulation' => '47600000',
                ),
            8 =>
                array (
                    'id' => '87',
                    'name' => 'User 9',
                    'auction_count' => '64',
                    'bid_accumulation' => '690350000',
                    'purchase_count' => '12',
                    'purchase_accumulation' => '26400000',
                    'sales_count' => '29',
                    'sales_accumulation' => '31500000',
                ),
            9 =>
                array (
                    'id' => '88',
                    'name' => 'User 10',
                    'auction_count' => '67',
                    'bid_accumulation' => '834900000',
                    'purchase_count' => '3',
                    'purchase_accumulation' => '7300000',
                    'sales_count' => '14',
                    'sales_accumulation' => '23800000',
                ),
        ));

        if ($participantNumber > $participants->count()) {
            throw new \Exception("Total Participant is not enough");
        }

        // Weight Criteria
        $weights = [
            'auction_count' => 4,
            'bid_accumulation' => 3,
            'purchase_count' => 2,
            'purchase_accumulation' => 2,
            'sales_count' => 1,
            'sales_accumulation' => 1
        ];

        // Normalization Weight
        $normWeights = [];
        foreach ($weights as $criteria => $weight) {
            $normWeights[$criteria] = $weight / array_sum($weights);
        }

//        print_r($normWeights);

        // Normalization Data
        $maxValues = [];
        foreach (array_keys($participants->first()) as $column) {
            if (!in_array($column,['id','name'])) {
                $maxValues[$column] = $participants->max($column);
            }
        }
//        print_r($maxValues);

        $normData = [];
        foreach ($participants as $participant) {
            $temp = [];
            foreach (array_keys($participant) as $column) {
                if (!in_array($column,['id','name'])) {
                    $temp[$column] = $participant[$column] / $maxValues[$column];
                } else {
                    $temp[$column] = $participant[$column];
                }
            }

            $normData[] = $temp;
        }
//        print_r($normData);


        // Calculation
        $calculations = [];
        foreach ($normData as $data) {
            $temp = [];

            foreach (array_keys($data) as $column) {
                if (!in_array($column,['id','name'])) {
                    $temp[] = pow($data[$column], $normWeights[$column]);
                }
            }

            $calculations[] = [
                'id' => $data['id'],
                'name' => $data['name'],
                'value' => array_product($temp)
            ];
        }
//        print_r($calculations);

        // Result normalization
        $sumCalculation = array_sum(array_column($calculations,'value'));
        $results = [];

        foreach ($calculations as $calculation) {
            $results[] = [
                'id' => $calculation['id'],
                'name' => $calculation['name'],
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
