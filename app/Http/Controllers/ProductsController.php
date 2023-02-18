<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Repositories\ProductRepository;
use App\Validators\ProductValidator;

/**
 * Class ProductsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ProductsController extends Controller
{
    public function __construct(protected UserRepository $repository) {

    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        return BaseResource::collection($this->repository->paginate($request->per_page));
    }

    public function show(Request $request, int $id): BaseResource
    {
        $user = $this->repository->scopeQuery(function($query){
            return $query->withTrashed();
        })->find($id);

        return new BaseResource($user);
    }
}
