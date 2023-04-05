<?php

namespace App\Criteria\Public;

use App\Utils\Constants;
use PHPUnit\TextUI\XmlConfiguration\Constant;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ProductCriteria.
 *
 * @package namespace App\Criteria\Public;
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
        return $model->whereIn('status',[
            Constants::PRODUCT_STATUS_APPROVED,
            Constants::PRODUCT_STATUS_ACTIVE,
            Constants::PRODUCT_STATUS_SOLD,
            Constants::PRODUCT_STATUS_CLOSED
        ]);
    }
}
