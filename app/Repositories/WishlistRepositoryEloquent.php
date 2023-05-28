<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WishlistRepository;
use App\Entities\Wishlist;
use App\Validators\WishlistValidator;

/**
 * Class WishlistRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WishlistRepositoryEloquent extends BaseRepository implements WishlistRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Wishlist::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
