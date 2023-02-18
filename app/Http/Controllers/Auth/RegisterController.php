<?php

namespace App\Http\Controllers\Auth;

use App\Entities\UserToken;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserVerifyRequest;
use App\Http\Resources\BaseResource;
use App\Notifications\FirstLoginNotification;
use App\Notifications\VerifyEmailNotification;
use App\Providers\RouteServiceProvider;
use App\Repositories\UserRepository;
use App\Utils\Constants;
use Carbon\Carbon;
use Faker\Provider\Base;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class RegisterController extends Controller
{
    public function __construct(protected UserRepository $repository) {
        
    }

    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        return BaseResource::collection($this->repository->paginate($request->per_page));
    }

    public function show(Request $request, int $id): BaseResource
    {
        $user = $this->repository->scopeQuery(function($query){
            return $query->withTrashed();
        })->find($id);

        return new BaseResource($user);
    }

    public function store(UserCreateRequest $request)
    {
        try {
            DB::beginTransaction();

            $request->merge([
                'password' => Hash::make($request->get('password')),
                'is_active' => false
            ]);

            $data = $this->repository->create($request->all());

            $data->roleUser()->create([
                'role_id' => Constants::ROLE_PUBLIC_ID,
            ]);

            $token = Str::random(50);
            $url = $request->get('url');

            $data->userToken()->create([
                'token' => $token
            ]);

            $data->notify(new VerifyEmailNotification($token, $url));

            DB::commit();

            return ($this->show($request, $data->id))->additional([
                'success' => true,
                'message' => 'Data created.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e, 500);
        }
    }

    public function verify(UserVerifyRequest $request)
    {
        try {
            DB::beginTransaction();

            $userToken = UserToken::query()
                ->where('token', $request->get('token'))
                ->first();

            $data = $this->repository->find($userToken->user_id);
            $data->update([
                'email_verified_at' => Carbon::now()->toDateTimeString(),
                'is_active' => true
            ]);
            $data->save();

            $data->customer()->create([
                'name' => $data->name,
                'email' => $data->email,
                'phone_number' => $data->phone_number,
            ]);

            $userToken->delete();

            $data->notify(new FirstLoginNotification());

            DB::commit();

            return ($this->show($request, $data->id))->additional([
                'success' => true,
                'message' => 'Data created.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json($e, 500);
        }
    }
}
