<?php

namespace App\Criteria;

use App\Utils\Constants;
use Illuminate\Support\Facades\Auth;
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

        return $model->whereNotIn('status',[
            Constants::ORDER_STATUS_EXPIRED,
            Constants::ORDER_STATUS_WAITING_PAYMENT
        ]);
    }
}
