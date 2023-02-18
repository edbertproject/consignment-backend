<?php

namespace App\Http\Controllers\Auth;

use App\Entities\User;
use App\Entities\UserToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePasswordRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Vodeamanager\Core\Utilities\Facades\ExceptionService;

class CreatePasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Create Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling create password requests.
    |
    */

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    public function createPassword(CreatePasswordRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = User::where('email', $request->get('email'))->first();

            $data->fill([
                'password' => Hash::make($request->get('password')),
                'is_active' => true,
            ]);
            $data->save();

            UserToken::where('user_id', $data->id)->delete();

            DB::commit();

            if ($request->wantsJson()) {
                return new JsonResponse(['message' => 'Your password has been created'], 200);
            }

            return redirect('/home')->with('status', 'Your password has been created');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json($e, 500);
        }
    }
}
