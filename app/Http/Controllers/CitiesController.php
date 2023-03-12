<?php

namespace App\Http\Controllers;

use App\Utils\Traits\RestControllerTrait;

use App\Repositories\CityRepository;
use App\Validators\CityValidator;

/**
 * Class CitiesController.
 *
 * @package namespace App\Http\Controllers;
 */
class CitiesController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(CityRepository $repository) {
        $this->__rest($repository);
    }
}
