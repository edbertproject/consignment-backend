<?php

namespace App\Http\Controllers;

use App\Criteria\OrderCriteria;
use App\Services\ExceptionService;
use App\Services\OrderService;
use App\Utils\Constants;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\OrderCreateRequest;
use App\Http\Requests\OrderUpdateRequest;
use App\Repositories\OrderRepository;

/**
 * Class OrdersController.
 *
 * @package namespace App\Http\Controllers;
 */
class OrdersController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(OrderRepository $repository) {
        $this->__rest($repository);

        $this->indexCriterias = [
            OrderCriteria::class
        ];
    }

    public function updateStatusComplete(Requests\OrderUpdateStatusRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $this->repository->update([
                'status' => $request->get('status'),
                'status_seller' => Constants::ORDER_SELLER_STATUS_COMPLETE,
                'status_buyer' => Constants::ORDER_BUYER_STATUS_COMPLETE,
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

    public function updateStatusSeller(Requests\OrderUpdateStatusSellerRequest $request, $id) {
        try {
            DB::beginTransaction();

            $data = $this->repository->update([
                'status_seller' => $request->get('status')
            ],$id);

            OrderService::handleUpdateStatusSeller($data);

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
