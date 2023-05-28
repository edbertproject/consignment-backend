<?php

namespace App\Criteria\Public;

use App\Utils\Constants;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class UserPartnerCriteria.
 *
 * @package namespace App\Criteria\Public;
 */
class UserPartnerCriteria implements CriteriaInterface
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
        return $model->whereHas('roles', function ($query) {
            $query->whereIn('role_id', [
                Constants::ROLE_PARTNER_ID
            ]);
        })->where('id', Auth::id());
    }
}
