<?php

namespace App\Http\Controllers\Public;

use App\Criteria\CartCriteria;
use App\Http\Controllers\Controller;
use App\Utils\Traits\RestControllerTrait;
use App\Repositories\PaymentMethodRepository;

/**
 * Class PaymentMethodsController.
 *
 * @package namespace App\Http\Controllers;
 */
class PaymentMethodsController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(PaymentMethodRepository $repository) {
        $this->__rest($repository);
    }
}
