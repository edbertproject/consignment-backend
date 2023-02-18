<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\DistrictCreateRequest;
use App\Http\Requests\DistrictUpdateRequest;
use App\Repositories\DistrictRepository;
use App\Validators\DistrictValidator;

/**
 * Class DistrictsController.
 *
 * @package namespace App\Http\Controllers;
 */
class DistrictsController extends Controller
{
    /**
     * @var DistrictRepository
     */
    protected $repository;

    /**
     * @var DistrictValidator
     */
    protected $validator;

    /**
     * DistrictsController constructor.
     *
     * @param DistrictRepository $repository
     * @param DistrictValidator $validator
     */
    public function __construct(DistrictRepository $repository, DistrictValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $districts = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $districts,
            ]);
        }

        return view('districts.index', compact('districts'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  DistrictCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(DistrictCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $district = $this->repository->create($request->all());

            $response = [
                'message' => 'District created.',
                'data'    => $district->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $district = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $district,
            ]);
        }

        return view('districts.show', compact('district'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $district = $this->repository->find($id);

        return view('districts.edit', compact('district'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  DistrictUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(DistrictUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $district = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'District updated.',
                'data'    => $district->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'District deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'District deleted.');
    }
}
