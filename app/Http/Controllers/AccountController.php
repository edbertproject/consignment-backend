<?php

namespace App\Http\Controllers;

use App\Criteria\Public\MyProductAuctionCriteria;
use App\Entities\User;
use App\Http\Requests\AccountUpdateRequest;
use App\Http\Resources\BaseResource;
use App\Http\Requests\AccountUpdatePasswordRequest;
use App\Repositories\ProductRepository;
use App\Services\ExceptionService;
use App\Services\MediaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function __construct(protected ProductRepository $productRepository)
    {
    }

    public function account(Request $request)
    {
        $user = $request->user();

        $data = User::with(['media','roles'])
            ->select('users.*')
            ->findOrFail($user->id);

        $data['role'] = $data->getRoleNames();
        $data['permission'] = $data->getAllPermissions()->pluck('name')->all();

        return new BaseResource($data);
    }

    public function update(AccountUpdateRequest $request)
    {
        try {
            DB::beginTransaction();

            $userFillable = (new User())->getFillable();

            $data = Auth::user();
            $data->fill($request->only($userFillable));
            $data->save();

            MediaService::sync($data,$request,['photo']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data updated.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }

    public function updatePassword(AccountUpdatePasswordRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = Auth::user();
            $data->fill([
                'password' => Hash::make($request->get('password')),
            ]);
            $data->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Password updated.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
        }
    }

    public function revoke(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
        ], 200);
    }

    public function readAllNotification(Request $request)
    {
        $user = $request->user();

        $user->unreadNotifications()
            ->update([
                'read_at' => Carbon::now(),
            ]);

        return response()->json([
            'success' => true,
        ], 200);
    }

    public function myAuction(Request $request) {
        $this->productRepository->pushCriteria(new MyProductAuctionCriteria($request));

        return BaseResource::collection($this->productRepository->paginate($request->per_page));
    }
}
