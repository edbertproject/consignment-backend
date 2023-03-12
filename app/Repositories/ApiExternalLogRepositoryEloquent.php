<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ApiExternalLogRepository;
use App\Entities\ApiExternalLog;
use App\Validators\ApiExternalLogValidator;

/**
 * Class ApiExternalLogRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ApiExternalLogRepositoryEloquent extends BaseRepository implements ApiExternalLogRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ApiExternalLog::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
