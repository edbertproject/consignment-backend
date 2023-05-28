<?php

namespace App\Http\Controllers\Public;

use App\Criteria\Public\UserPartnerCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\UserPartnerCreateRequest;
use App\Http\Resources\UserPartnerShowResource;
use App\Repositories\UserRepository;
use App\Services\ExceptionService;
use App\Services\MediaService;
use App\Utils\Constants;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                'is_active' => false,
                'status' => Constants::PARTNER_STATUS_WAITING_APPROVAL
            ]);

            $data = Auth::user();
            $data->partner()->updateOrCreate($request->all());

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
}
