<?php

namespace App\Http\Controllers;

use App\Criteria\UserPublicCriteria;
use App\Repositories\UserRepository;
use App\Utils\Traits\RestControllerTrait;

/**
 * Class UsersController.
 *
 * @package namespace App\Http\Controllers;
 */
class UserPublicController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(UserRepository $repository) {
        $this->__rest($repository);

        $this->indexCriterias = [
            UserPublicCriteria::class
        ];
    }
}
