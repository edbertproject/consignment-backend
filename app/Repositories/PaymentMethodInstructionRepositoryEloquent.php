<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PaymentMethodInstructionRepository;
use App\Entities\PaymentMethodInstruction;
use App\Validators\PaymentMethodInstructionValidator;

/**
 * Class PaymentMethodInstructionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PaymentMethodInstructionRepositoryEloquent extends BaseRepository implements PaymentMethodInstructionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PaymentMethodInstruction::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
