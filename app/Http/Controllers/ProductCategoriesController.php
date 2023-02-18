<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Http\Resources\BaseResource;
use App\Repositories\UserRepository;
use App\Services\ExceptionService;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Http\Request;
use Exception;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ProductCategoryCreateRequest;
use App\Http\Requests\ProductCategoryUpdateRequest;
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

    public function store(ProductCategoryCreateRequest $request) {
        try {
            DB::beginTransaction();

            $data = $this->repository->create($request->all());

            DB::commit();

            return ($this->show($request, $data->id))->additional([
                'success' => true,
                'message' => 'Data created.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }

    public function update(ProductCategoryUpdateRequest $request, int $id) {
        try {
            DB::beginTransaction();

            $data = $this->repository->update($request->all(),$id);

            DB::commit();

            return ($this->show($request, $data->id))->additional([
                'success' => true,
                'message' => 'Data updated.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }
}
