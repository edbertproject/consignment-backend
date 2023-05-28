<?php

namespace App\Repositories;

use App\Criteria\RestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\DistrictRepository;
use App\Entities\District;
use App\Validators\DistrictValidator;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class DistrictRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DistrictRepositoryEloquent extends BaseRepository implements DistrictRepository
{
    use CacheableRepository;

    protected $fieldSearchable = [
        'name',
        'city_id'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return District::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RestCriteria::class));
    }

}
