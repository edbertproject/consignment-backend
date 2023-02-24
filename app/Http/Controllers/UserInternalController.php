<?php

namespace App\Http\Controllers;

use App\Criteria\UserInternalCriteria;
use App\Http\Requests\UserInternalCreateRequest;
use App\Http\Requests\UserInternalUpdateRequest;
use App\Repositories\UserRepository;
use App\Services\ExceptionService;
use App\Services\MediaService;
use App\Utils\Helper;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class UsersController.
 *
 * @package namespace App\Http\Controllers;
 */
class UserInternalController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(UserRepository $repository) {
        $this->__rest($repository);

        $this->indexCriterias = [
            UserInternalCriteria::class
        ];
    }

    public function store(UserInternalCreateRequest $request) {
        try {
            DB::beginTransaction();

            $request->merge([
                'is_active' => true,
                'password' => Hash::make($request->get('password'))
            ]);

            $data = $this->repository->create($request->all());

            $data->syncRoles(Helper::arrayStrict($request->get('role_id')));

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

    public function update(UserInternalUpdateRequest $request, int $id) {
        try {
            DB::beginTransaction();

            $request->merge([
                'password' => Hash::make($request->get('password'))
            ]);

            $data = $this->repository->update($request->all(),$id);

            $data->syncRoles(Helper::arrayStrict($request->get('role_id')));

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
}
