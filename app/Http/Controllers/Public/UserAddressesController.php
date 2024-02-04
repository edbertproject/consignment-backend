<?php

namespace App\Http\Controllers\Public;

use App\Criteria\Public\UserAddressCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\UserAddressCreateRequest;
use App\Http\Requests\Public\UserAddressUpdateRequest;
use App\Repositories\UserAddressRepository;
use App\Services\ExceptionService;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserAddressesController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(UserAddressRepository $repository) {
        $this->__rest($repository);

        $this->indexCriterias = [
            UserAddressCriteria::class
        ];
    }

    public function store(UserAddressCreateRequest $request)
    {
        try {
            DB::beginTransaction();

            $request->merge([
                'user_id' => Auth::id(),
                'is_primary' => !Auth::user()->addresses()->exists()
            ]);

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

    public function update(UserAddressUpdateRequest $request,$id)
    {
        try {
            DB::beginTransaction();

            $request->merge([
                'user_id' => Auth::id()
            ]);

            $data = $this->repository->update($request->all(),$id);

            DB::commit();

            return ($this->show($request, $data->id))->additional([
                'success' => true,
                'message' => 'Data updated.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }
}
