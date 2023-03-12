<?php

namespace App\Http\Controllers;

use App\Utils\Traits\RestControllerTrait;
use App\Repositories\DistrictRepository;
use App\Validators\DistrictValidator;

/**
 * Class DistrictsController.
 *
 * @package namespace App\Http\Controllers;
 */
class DistrictsController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(DistrictRepository $repository) {
        $this->__rest($repository);
    }
}
