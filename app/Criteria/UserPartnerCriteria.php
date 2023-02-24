<?php

namespace App\Criteria;

use App\Utils\Constants;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class UserPublicCriteria.
 *
 * @package namespace App\Criteria;
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
            });
    }
}
