<?php

namespace App\Criteria;

use App\Utils\Constants;
use Illuminate\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ForbiddenRoleCriteria.
 *
 * @package namespace App\Criteria;
 */
class ForbiddenRoleSelectCriteria implements CriteriaInterface
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
        return $model->whereNotIn('id', [
            Constants::ROLE_PUBLIC_ID,
            Constants::ROLE_PARTNER_ID
        ]);
    }
}
