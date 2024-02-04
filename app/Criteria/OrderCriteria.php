<?php

namespace App\Criteria;

use App\Utils\Constants;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class OrderCriteria.
 *
 * @package namespace App\Criteria;
 */
class OrderCriteria implements CriteriaInterface
{
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
        if (Auth::user()->hasRole(Constants::ROLE_PARTNER_ID)) {
            $model = $model->where('partner_id',@Auth::user()->partner->id);
        }

        return $model->leftJoin(DB::raw('LATERAL (
            SELECT order_statuses.status, order_statuses.order_id
            FROM order_statuses
            WHERE order_statuses.order_id = orders.id
            AND order_statuses.type = "Primary"
            ORDER BY order_statuses.created_at DESC
            LIMIT 1
        ) AS last_statuses'),'last_statuses.order_id','orders.id')
            ->whereNotIn('last_statuses.status',[
                Constants::ORDER_STATUS_EXPIRED,
                Constants::ORDER_STATUS_WAITING_PAYMENT
            ]);
    }
}
