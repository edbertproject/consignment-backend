<?php

namespace App\Repositories;

use App\Criteria\RestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CartRepository;
use App\Entities\Cart;
use App\Validators\CartValidator;

/**
 * Class CartRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CartRepositoryEloquent extends BaseRepository implements CartRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Cart::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RestCriteria::class));
    }

}
