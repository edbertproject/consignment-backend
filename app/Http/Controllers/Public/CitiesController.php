<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Repositories\CityRepository;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(CityRepository $repository) {
        $this->__rest($repository);
    }
}
