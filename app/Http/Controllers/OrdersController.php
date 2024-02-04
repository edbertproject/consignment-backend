<?php

namespace App\Http\Controllers;

use App\Criteria\OrderCriteria;
use App\Entities\OrderStatus;
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

            OrderService::updateStatus($id,$request->get('status'));
            OrderService::updateStatus($id,Constants::ORDER_SELLER_STATUS_COMPLETE, Constants::ORDER_STATUS_TYPE_SELLER);
            OrderService::updateStatus($id,Constants::ORDER_BUYER_STATUS_COMPLETE, Constants::ORDER_STATUS_TYPE_BUYER);

            DB::commit();
            return ($this->show($request, $id))->additional([
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

            $orderStatus = OrderService::updateStatus($id,$request->get('status'),Constants::ORDER_STATUS_TYPE_SELLER);

            OrderService::handleUpdateStatusSeller($orderStatus->status, $id);

            DB::commit();

            return ($this->show($request, $id))->additional([
                'success' => true,
                'message' => 'Data status updated.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }
}
