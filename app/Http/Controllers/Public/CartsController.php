<?php

namespace App\Http\Controllers\Public;

use App\Criteria\CartCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\CartUpdateRequest;
use App\Services\ExceptionService;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CartCreateRequest;
use App\Repositories\CartRepository;

/**
 * Class CartsController.
 *
 * @package namespace App\Http\Controllers;
 */
class CartsController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(CartRepository $repository) {
        $this->__rest($repository);

        $this->indexCriterias = [
            CartCriteria::class
        ];
    }

    public function store(CartCreateRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $this->repository->create($request->all());

            DB::commit();

            return ($this->show($request, $data->id))->additional([
                'success' => true,
                'message' => 'Data created.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }

    public function update(CartUpdateRequest $request)
    {
        try {
            DB::beginTransaction();

            $this->repository->deleteWhere([
                'user_id' => Auth::id()
            ]);

            foreach ($request->get('carts') as $cart) {
                $this->repository->create(array_merge([
                    'user_id' => Auth::id(),
                ],$cart));
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data updated.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }
}
