<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Http\Requests\AccountUpdateRequest;
use App\Http\Resources\BaseResource;
use App\Http\Requests\AccountUpdatePasswordRequest;
use App\Services\ExceptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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
            ->findOrFail($user->id);

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
                'password' => Hash::make($request->get('new_password')),
            ]);
            $data->save();

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
                'read_at' => \Illuminate\Support\Carbon::now(),
            ]);

        return response()->json([
            'success' => true,
        ], 200);
    }
}
