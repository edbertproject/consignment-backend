<?php

namespace App\Http\Controllers;

use App\Criteria\UserPartnerCriteria;
use App\Http\Requests\UserPartnerCreateRequest;
use App\Http\Requests\UserPartnerUpdateRequest;
use App\Http\Requests\UserPartnerUpdateStatusRequest;
use App\Http\Resources\UserPartnerShowResource;
use App\Repositories\PartnerRepository;
use App\Repositories\UserRepository;
use App\Services\ExceptionService;
use App\Services\MediaService;
use App\Utils\Constants;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class UsersController.
 *
 * @package namespace App\Http\Controllers;
 */
class UserPartnerController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(UserRepository $repository) {
            $this->__rest($repository);

            $this->indexCriterias = [
                UserPartnerCriteria::class
            ];

            $this->showResource = UserPartnerShowResource::class;
        }

    public function store(UserPartnerCreateRequest $request) {
        try {
            DB::beginTransaction();

            $request->merge([
                'is_active' => true
            ]);

            $data = $this->repository->create($request->all());

            $data->partner()->updateOrCreate($request->all());

            $data->syncRoles([Constants::ROLE_PARTNER_ID]);

            MediaService::sync($data,$request,['photo']);

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

    public function update(UserPartnerUpdateRequest $request, int $id) {
        try {
            DB::beginTransaction();

            $data = $this->repository->update($request->all(),$id);

            $data->partner()->updateOrCreate($request->all());

            MediaService::sync($data,$request,['photo']);

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

    public function updateStatus(UserPartnerUpdateStatusRequest $request, int $id) {
        try {
            DB::beginTransaction();

            $data = $this->repository->find($id);

            if ($request->status === Constants::PARTNER_STATUS_APPROVED) {
                $data->syncRoles([Constants::ROLE_PARTNER_ID]);
                $data->is_active = true;

                // partner approved notification
            } else {
                // partner reject notification
            }

            $data->status = $request->status;
            $data->save();
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
