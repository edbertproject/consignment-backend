<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\UserAddressCreateRequest;
use App\Http\Requests\UserAddressUpdateRequest;
use App\Repositories\UserAddressRepository;
use App\Validators\UserAddressValidator;

/**
 * Class UserAddressesController.
 *
 * @package namespace App\Http\Controllers;
 */
class UserAddressesController extends Controller
{
    /**
     * @var UserAddressRepository
     */
    protected $repository;

    /**
     * @var UserAddressValidator
     */
    protected $validator;

    /**
     * UserAddressesController constructor.
     *
     * @param UserAddressRepository $repository
     * @param UserAddressValidator $validator
     */
    public function __construct(UserAddressRepository $repository, UserAddressValidator $validator)
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
        $userAddresses = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $userAddresses,
            ]);
        }

        return view('userAddresses.index', compact('userAddresses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserAddressCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(UserAddressCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $userAddress = $this->repository->create($request->all());

            $response = [
                'message' => 'UserAddress created.',
                'data'    => $userAddress->toArray(),
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
        $userAddress = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $userAddress,
            ]);
        }

        return view('userAddresses.show', compact('userAddress'));
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
        $userAddress = $this->repository->find($id);

        return view('userAddresses.edit', compact('userAddress'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UserAddressUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(UserAddressUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $userAddress = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'UserAddress updated.',
                'data'    => $userAddress->toArray(),
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
                'message' => 'UserAddress deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'UserAddress deleted.');
    }
}
