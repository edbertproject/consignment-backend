<?php

namespace App\Http\Controllers;

use App\Criteria\ForbiddenRoleIndexCriteria;
use App\Criteria\ForbiddenRoleSelectCriteria;
use App\Http\Requests\RoleCreateRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Repositories\RoleRepository;
use App\Services\ExceptionService;
use App\Utils\Constants;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(RoleRepository $repository) {
        $this->__rest($repository);

        $this->indexCriterias = [
            ForbiddenRoleIndexCriteria::class
        ];

        $this->selectCriterias = [
            ForbiddenRoleSelectCriteria::class
        ];
    }

    public function store(RoleCreateRequest $request) {
        try {
            DB::beginTransaction();

            $request->merge([
                'is_admin' => false,
                'guard_name' => 'api'
            ]);

            $data = $this->repository->create($request->all());

            $data->syncPermissions($request->get('permission_ids'));

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

    public function update(RoleUpdateRequest $request, int $id) {
        try {
            DB::beginTransaction();

            if (in_array($id,[Constants::ROLE_PUBLIC_ID, Constants::ROLE_PARTNER_ID, Constants::ROLE_SUPER_ADMIN_ID])) {
                return ExceptionService::responseJson(new \Exception("Forbidden id",422));
            }

            $data = $this->repository->update($request->all(),$id);

            $data->syncPermissions($request->get('permission_ids'));

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

    public function destroy(Request $request, $id){
        try {
            DB::beginTransaction();

            if (in_array($id,[Constants::ROLE_PUBLIC_ID, Constants::ROLE_PARTNER_ID, Constants::ROLE_SUPER_ADMIN_ID])) {
                return ExceptionService::responseJson(new \Exception("Forbidden id",422));
            }

            $deleted = $this->repository->delete($id);
            DB::commit();

            return response()->json([
                'success' => $deleted
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return ExceptionService::responseJson($e);
        }
    }
}
