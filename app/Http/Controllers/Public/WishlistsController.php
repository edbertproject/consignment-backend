<?php

namespace App\Http\Controllers\Public;

use App\Criteria\Public\WishlistCriteria;
use App\Http\Controllers\Controller;
use App\Http\Requests\WishlistCreateRequest;
use App\Http\Requests\WishlistUpdateRequest;
use App\Repositories\WishlistRepository;
use App\Services\ExceptionService;
use App\Utils\Traits\RestControllerTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistsController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(WishlistRepository $repository) {
        $this->__rest($repository);

        $this->indexCriterias = [
            WishlistCriteria::class
        ];
    }

    public function inWishlist(Request $request, $id) {
        $exists = $this->repository->where('user_id', Auth::id())
            ->where('product_id', $id)
            ->exists();

        return response()->json([
            "success" => true,
            "data" => $exists
        ]);
    }

    public function store(WishlistCreateRequest $request)
    {
        try {
            DB::beginTransaction();

            $request->merge([
                'user_id' => Auth::id(),
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

    public function destroy(Request $request, $id){
        try {
            DB::beginTransaction();

            foreach ($this->indexCriterias as $indexCriteria) {
                $this->repository->pushCriteria(new $indexCriteria($request));
            }

            $deleted = $this->repository->deleteWhere([
                'product_id' => $id
            ]);
            DB::commit();

            return response()->json([
                'success' => boolval($deleted)
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return ExceptionService::responseJson($e);
        }
    }
}
