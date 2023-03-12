<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Repositories\OrderRepository;
use App\Validators\OrderValidator;

/**
 * Class OrdersController.
 *
 * @package namespace App\Http\Controllers;
 */
class OrdersController extends Controller
{
    use RestCoreController {
        RestCoreController::__construct as private __restConstruct;
    }

    public function __construct(Booking $repository)
    {
        $this->repository = $repository;
        $this->__restConstruct();
    }
}
