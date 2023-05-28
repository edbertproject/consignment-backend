<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ProductParticipantRepository;
use App\Entities\ProductParticipant;
use App\Validators\ProductParticipantValidator;

/**
 * Class ProductParticipantRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ProductParticipantRepositoryEloquent extends BaseRepository implements ProductParticipantRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProductParticipant::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
