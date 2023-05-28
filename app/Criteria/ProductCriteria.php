<?php

namespace App\Criteria;

use App\Utils\Constants;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ProductCriteria.
 *
 * @package namespace App\Criteria;
 */
class ProductCriteria implements CriteriaInterface
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
            return $model->where(function ($q) {
                $q->where('partner_id',@Auth::user()->partner->id)->orWhere('created_by',Auth::id());
            });
        }

        return $model;
    }
}
