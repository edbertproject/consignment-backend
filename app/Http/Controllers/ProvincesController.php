<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ProvinceCreateRequest;
use App\Http\Requests\ProvinceUpdateRequest;
use App\Repositories\ProvinceRepository;
use App\Validators\ProvinceValidator;

/**
 * Class ProvincesController.
 *
 * @package namespace App\Http\Controllers;
 */
class ProvincesController extends Controller
{
    /**
     * @var ProvinceRepository
     */
    protected $repository;

    /**
     * @var ProvinceValidator
     */
    protected $validator;

    /**
     * ProvincesController constructor.
     *
     * @param ProvinceRepository $repository
     * @param ProvinceValidator $validator
     */
    public function __construct(ProvinceRepository $repository, ProvinceValidator $validator)
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
        $provinces = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $provinces,
            ]);
        }

        return view('provinces.index', compact('provinces'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ProvinceCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ProvinceCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $province = $this->repository->create($request->all());

            $response = [
                'message' => 'Province created.',
                'data'    => $province->toArray(),
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
        $province = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $province,
            ]);
        }

        return view('provinces.show', compact('province'));
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
        $province = $this->repository->find($id);

        return view('provinces.edit', compact('province'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ProvinceUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ProvinceUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $province = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Province updated.',
                'data'    => $province->toArray(),
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
                'message' => 'Province deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Province deleted.');
    }
}
