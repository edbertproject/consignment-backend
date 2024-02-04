<?php

namespace App\Http\Controllers;

use App\Criteria\ProductCriteria;
use App\Criteria\UserInternalCriteria;
use App\Entities\User;
use App\Http\Resources\BaseResource;
use App\Repositories\UserRepository;
use App\Services\ExceptionService;
use App\Services\MediaService;
use App\Services\ProductService;
use App\Utils\Constants;
use App\Utils\Helper;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        $this->indexCriterias = [
            ProductCriteria::class
        ];
    }

    public function store(ProductCreateRequest $request) {
        try {
            DB::beginTransaction();

            $request->merge([
                'status' => Constants::PRODUCT_STATUS_APPROVED,
                'slug' => Str::slug($request->name . Str::random('4')),
                'available_quantity' => $request->quantity
            ]);

            if (Auth::user()->hasRole(Constants::ROLE_PARTNER_ID)) {
                $request->merge([
                    'status' => Constants::PRODUCT_STATUS_WAITING_APPROVAL,
                    'partner_id' => Auth::user()->partner->id
                ]);
            }

            $data = $this->repository->create($request->all());

            $data->participants()->sync($request->get('eligible_participants'));

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

            $request->merge([
                'slug' => Str::slug($request->name),
                'available_quantity' => $request->quantity
            ]);

            if (Auth::user()->hasRole(Constants::ROLE_PARTNER_ID)) {
                $request->merge([
                    'status' => Constants::PRODUCT_STATUS_WAITING_APPROVAL,
                ]);
            }

            $data = $this->repository->update($request->all(),$id);

            $data->participants()->sync($request->get('eligible_participants'));

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

    public function getEligibleParticipants(Request $request) {
        $participants = ProductService::determineParticipantAuction($request->get('participant'));

        $ids = array_column($participants,'id');

        $placeholders = implode(',',array_fill(0, count($ids), '?'));
        return BaseResource::collection(
            User::query()->whereIn('id',$ids)
                ->orderByRaw("field(id,{$placeholders})", $ids)->get(),
        );
    }

    public function updateStatus(Requests\ProductUpdateStatusRequest $request, int $id) {
        try {
            DB::beginTransaction();

            $data = $this->repository->update([
                'status' => $request->get('status')
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

    public function cancel(Requests\ProductCancelRequest $request, int $id) {
        try {
            DB::beginTransaction();

            $entity = $this->repository->find($id);

            if ($entity->status === Constants::PRODUCT_STATUS_WAITING_APPROVAL) {
                $entity->update([
                    'status' => Constants::PRODUCT_STATUS_CANCEL_APPROVED,
                ]);
            } else {
                $entity->update([
                    'status' => Constants::PRODUCT_STATUS_WAITING_CANCEL_APPROVAL,
                    'cancel_reason' => $request->get('cancel_reason')
                ]);
            }

            DB::commit();

            return ($this->show($request, $entity->id))->additional([
                'success' => true,
                'message' => 'Data updated.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }
}
