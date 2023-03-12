<?php

namespace App\Utils\Traits;

use App\Criteria\RestCriteria;
use App\Http\Resources\BaseResource;
use App\Services\ExceptionService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

trait RestControllerTrait
{
    public $repository;
    protected array $indexCriterias = [];
    protected array $selectCriterias = [];
    protected $indexResource = BaseResource::class;
    protected $showResource = BaseResource::class;

    public function __construct($repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        foreach ($this->indexCriterias as $indexCriteria) {
            $this->repository->pushCriteria(new $indexCriteria($request));
        }

        return $this->indexResource::collection($this->repository->paginate($request->per_page));
    }

    public function select(Request $request)
    {
        if (empty($this->selectCriterias)) {
            foreach ($this->indexCriterias as $indexCriteria) {
                $this->repository->pushCriteria(new $indexCriteria($request));
            }
        } else {
            foreach ($this->selectCriterias as $selectCriteria) {
                $this->repository->pushCriteria(new $selectCriteria($request));
            }
        }

        return $this->indexResource::collection($this->repository->paginate($request->per_page));
    }

    public function show(Request $request, int $id)
    {
        foreach ($this->indexCriterias as $indexCriteria) {
            $this->repository->pushCriteria(new $indexCriteria($request));
        }

        $user = $this->repository->find($id);

        return new $this->showResource($user);
    }

    public function destroy(Request $request, $id){
        try {
            DB::beginTransaction();

            foreach ($this->indexCriterias as $indexCriteria) {
                $this->repository->pushCriteria(new $indexCriteria($request));
            }

            $deleted = $this->repository->delete($id);
            DB::commit();

            return response()->json([
                'success' => $deleted
            ]);
        } catch (Exception $e) {
            DB::rollback();
            return ExceptionService::responseJson($e);
        }
    }
}
