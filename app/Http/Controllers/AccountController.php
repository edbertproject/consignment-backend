<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\AccountUpdatePasswordRequest;
use App\Http\Requests\Admin\AccountUpdateRequest;
use App\Http\Resources\AccountResource;
use App\Http\Resources\Frontend\AccountResource as FrontendAccountResource;
use App\Entities\User;
use App\Utils\Constants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Vodeamanager\Core\Utilities\Facades\ExceptionService;
use Vodeamanager\Core\Utilities\Facades\ResourceService;

class AccountController extends Controller
{
    protected $lazyLoadingRelationAccount = [
        // 'roleUser.role',
        'roles',
        'photo',
        // 'customer',
    ];

    public function account(Request $request)
    {
        $user = $request->user();

        $data = User::with(['photo'])
            ->select('users.*')
//            ->addSelect('customers.id AS customer_id')
//            ->addSelect('customers.parent_id AS parent_id')
//            ->addSelect('customers.date_of_birth AS date_of_birth')
//            ->addSelect('customers.gender AS gender')
//            ->addSelect('customers.category AS category')
//            ->addSelect('customers.address AS address')
//            ->addSelect('customers.relation AS relation')
//            ->addSelect('customers.photo_id AS photo_id')
//            ->addSelect(DB::raw('IF(customers.date_of_birth IS NULL, 0, 1) AS is_completed'))
//            ->leftJoin('customers', 'customers.user_id', 'users.id')
            ->findOrFail($user->id);

        if($user->roleUser->role_id != Constant::ROLE_CUSTOMER) {
            return ResourceService::jsonResource(AccountResource::class, $data);
        } else {
            return ResourceService::jsonResource(FrontendAccountResource::class, $data);
        }
    }

    public function update(AccountUpdateRequest $request)
    {
        try {
            DB::beginTransaction();

            $userFillAble = (new User())->getFillable();

            $data = Auth::user();
            $data->fill($request->only($userFillAble));
            $data->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data updated.'
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
                'password' => Hash::make($request->get('new_password'))
            ]);
            $data->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data updated.'
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

    /* public function notification(Request $request)
    {
        return response()->json([
            'data' => Auth::user()->notifications,
            'total_unread_notification' => count(Auth::user()->unreadNotifications)
        ]);
    } */

    public function readAllNotification(Request $request)
    {
        $user = $request->user();

        $user->unreadNotifications()
            ->update([
                'read_at' => \Illuminate\Support\Carbon::now(),
            ]);

        return response()->json([
            'success' => true
        ],200);
    }
}
