<?php

namespace App\Criteria\Public;

use App\Utils\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class MyProductAuctionCriteria.
 *
 * @package namespace App\Criteria\Public;
 */
class MyProductAuctionCriteria implements CriteriaInterface
{
    public function __construct(protected Request $request) {
    }

    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $selects = [
            DB::raw("(IF(winner_id=".Auth::id()." and status='".Constants::PRODUCT_STATUS_CLOSED."',true,false)) AS can_pay"),
            DB::raw("(IF(winner_id=".Auth::id().",DATE_ADD(products.end_date, INTERVAL ".Constants::PRODUCT_AUCTION_EXPIRES." HOUR),NULL)) AS expire_pay_date"),
            DB::raw("(
                IF(winner_id=".Auth::id().",
                    IF(products.end_date > DATE_ADD(products.end_date, INTERVAL ".Constants::PRODUCT_AUCTION_EXPIRES." HOUR),'EXPIRED','WINNER')
                ,'LOSE')
            ) AS status")
        ];

        foreach ($selects as $select) {
            $model = $model->addSelect($select);
        }

        return $model
            ->where(function ($where) {
                $where->whereHas('bids', function ($bid) {
                    $bid->where('user_id',Auth::id());
                })->orWhere('winner_id',Auth::id());
            });
    }
}
