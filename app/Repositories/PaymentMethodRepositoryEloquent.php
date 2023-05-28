<?php

namespace App\Repositories;

use App\Criteria\RestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PaymentMethodRepository;
use App\Entities\PaymentMethod;
use App\Validators\PaymentMethodValidator;

/**
 * Class PaymentMethodRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PaymentMethodRepositoryEloquent extends BaseRepository implements PaymentMethodRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PaymentMethod::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RestCriteria::class));
    }

}
