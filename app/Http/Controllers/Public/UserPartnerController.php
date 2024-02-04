<?php

namespace App\Http\Controllers\Public;

use App\Criteria\Public\UserPartnerCriteria;
use App\Entities\UserAddress;
use App\Http\Controllers\Controller;
use App\Http\Requests\Public\UserPartnerCreateRequest;
use App\Http\Resources\UserPartnerShowResource;
use App\Notifications\PartnerPendingNotification;
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

            $userAddress = UserAddress::find($request->user_address_id);

            $data = Auth::user();

            $request->merge([
                'user_address_id' => $userAddress->id,
                'full_address' => $userAddress->full_address,
                'postal_code' => $userAddress->postal_code,
                'province_id' => $userAddress->province_id,
                'city_id' => $userAddress->city_id,
                'district_id' => $userAddress->district_id,
                'status' => Constants::PARTNER_STATUS_WAITING_APPROVAL
            ]);

            $data->partner()->updateOrCreate([
                'id' => !empty($data->partner) ? $data->partner->id : null
            ], $request->all());

            $data->notify(new PartnerPendingNotification());

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data created.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }
}
