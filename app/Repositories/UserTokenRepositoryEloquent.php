<?php

namespace App\Repositories;

use App\Criteria\RestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserTokenRepository;
use App\Entities\UserToken;
use App\Validators\UserTokenValidator;

/**
 * Class UserTokenRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserTokenRepositoryEloquent extends BaseRepository implements UserTokenRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserToken::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RestCriteria::class));
    }

}
