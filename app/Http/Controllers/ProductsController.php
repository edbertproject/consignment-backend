<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Repositories\UserRepository;
use App\Services\ExceptionService;
use App\Services\MediaService;
use App\Utils\Constants;
use App\Utils\Helper;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ProductCreateRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Repositories\ProductRepository;

/**
 * Class ProductsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ProductsController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(ProductRepository $repository) {
        $this->__rest($repository);
    }

    public function store(ProductCreateRequest $request) {
        try {
            DB::beginTransaction();

            $request->merge([
                'status' => Constants::PRODUCT_STATUS_APPROVED,
            ]);

            $data = $this->repository->create($request->all());

            MediaService::sync($data,$request,['photos']);

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

    public function update(ProductUpdateRequest $request, int $id) {
        try {
            DB::beginTransaction();

            $data = $this->repository->update($request->all(),$id);

            MediaService::sync($data,$request,['photos']);

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

    public function updateStatus(Requests\ProductUpdateStatusRequest $request, int $id) {
        try {
            DB::beginTransaction();

            $data = $this->repository->update([
                'status' => $request->get('status'),
                'cancel_reason' => $request->get('cancel_reason')
            ],$id);

            DB::commit();

            return ($this->show($request, $data->id))->additional([
                'success' => true,
                'message' => 'Data status updated.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }
}
