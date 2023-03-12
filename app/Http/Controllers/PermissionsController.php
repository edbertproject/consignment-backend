<?php

namespace App\Http\Controllers;

use App\Utils\Traits\RestControllerTrait;
use App\Repositories\PermissionRepository;
use App\Validators\PermissionValidator;

/**
 * Class PermissionsController.
 *
 * @package namespace App\Http\Controllers;
 */
class PermissionsController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(PermissionRepository $repository) {
        $this->__rest($repository);
    }
}
