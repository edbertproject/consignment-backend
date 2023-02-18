<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\CourierCreateRequest;
use App\Http\Requests\CourierUpdateRequest;
use App\Repositories\CourierRepository;
use App\Validators\CourierValidator;

/**
 * Class CouriersController.
 *
 * @package namespace App\Http\Controllers;
 */
class CouriersController extends Controller
{
    /**
     * @var CourierRepository
     */
    protected $repository;

    /**
     * @var CourierValidator
     */
    protected $validator;

    /**
     * CouriersController constructor.
     *
     * @param CourierRepository $repository
     * @param CourierValidator $validator
     */
    public function __construct(CourierRepository $repository, CourierValidator $validator)
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
        $couriers = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $couriers,
            ]);
        }

        return view('couriers.index', compact('couriers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CourierCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CourierCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $courier = $this->repository->create($request->all());

            $response = [
                'message' => 'Courier created.',
                'data'    => $courier->toArray(),
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
        $courier = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $courier,
            ]);
        }

        return view('couriers.show', compact('courier'));
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
        $courier = $this->repository->find($id);

        return view('couriers.edit', compact('courier'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CourierUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(CourierUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $courier = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Courier updated.',
                'data'    => $courier->toArray(),
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
                'message' => 'Courier deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Courier deleted.');
    }
}
