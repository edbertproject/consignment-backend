<?php

namespace App\Http\Controllers;

use App\Criteria\UserPartnerCriteria;
use App\Http\Requests\UserPartnerApproveRequest;
use App\Http\Requests\UserPartnerCreateRequest;
use App\Http\Requests\UserPartnerRejectedRequest;
use App\Http\Requests\UserPartnerUpdateRequest;
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

    public function approve(UserPartnerApproveRequest $request, int $id) {
        try {
            DB::beginTransaction();

            $data = $this->repository->update([
                'status' => Constants::PARTNER_STATUS_APPROVED
            ],$id);

            DB::commit();

            return ($this->show($request, $data->id))->additional([
                'success' => true,
                'message' => 'Data approved.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }

    public function reject(UserPartnerRejectedRequest $request, int $id) {
        try {
            DB::beginTransaction();

            $data = $this->repository->update([
                'status' => Constants::PARTNER_STATUS_REJECTED
            ],$id);

            DB::commit();

            return ($this->show($request, $data->id))->additional([
                'success' => true,
                'message' => 'Data approved.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }
}
