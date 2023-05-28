<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Utils\Traits\RestControllerTrait;
use App\Repositories\ProductCategoryRepository;

/**
 * Class ProductCategoriesController.
 *
 * @package namespace App\Http\Controllers;
 */
class ProductCategoriesController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(ProductCategoryRepository $repository) {
        $this->__rest($repository);
    }
}
