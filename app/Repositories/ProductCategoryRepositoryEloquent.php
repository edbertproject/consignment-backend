<?php

namespace App\Repositories;

use App\Criteria\RestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProductCategoryRepository;
use App\Entities\ProductCategory;
use App\Validators\ProductCategoryValidator;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class ProductCategoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductCategoryRepositoryEloquent extends BaseRepository implements ProductCategoryRepository
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'name' => 'like',
        'code' => 'like',
    ];


    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductCategory::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RestCriteria::class));
    }

}
