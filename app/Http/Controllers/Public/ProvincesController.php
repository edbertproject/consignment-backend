<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Repositories\ProvinceRepository;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Http\Request;

class ProvincesController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(ProvinceRepository $repository) {
        $this->__rest($repository);
    }
}
