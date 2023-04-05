<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProductBidRepository;
use App\Entities\ProductBid;
use App\Validators\ProductBidValidator;

/**
 * Class ProductBidRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductBidRepositoryEloquent extends BaseRepository implements ProductBidRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductBid::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
