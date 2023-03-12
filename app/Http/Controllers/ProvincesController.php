<?php

namespace App\Http\Controllers;

use App\Utils\Traits\RestControllerTrait;
use App\Repositories\ProvinceRepository;
use App\Validators\ProvinceValidator;

/**
 * Class ProvincesController.
 *
 * @package namespace App\Http\Controllers;
 */
class ProvincesController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(ProvinceRepository $repository) {
        $this->__rest($repository);
    }
}
