<?php

namespace App\Criteria;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class CartCriteria.
 *
 * @package namespace App\Criteria;
 */
class CartCriteria implements CriteriaInterface
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
        return $model->select('carts.*')
            ->addSelect('products.name AS product_name')
            ->addSelect('products.price AS price')
            ->addSelect('products.available_quantity AS stock')
            ->addSelect('product_categories.name AS category_name')
            ->join('products', 'products.id', '=', 'carts.product_id')
            ->join('product_categories', 'product_categories.id', '=', 'products.product_category_id')
            ->where('user_id', Auth::user()->id);
    }
}
