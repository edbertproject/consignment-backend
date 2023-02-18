<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\PartnerCreateRequest;
use App\Http\Requests\PartnerUpdateRequest;
use App\Repositories\PartnerRepository;
use App\Validators\PartnerValidator;

/**
 * Class PartnersController.
 *
 * @package namespace App\Http\Controllers;
 */
class PartnersController extends Controller
{
    /**
     * @var PartnerRepository
     */
    protected $repository;

    /**
     * @var PartnerValidator
     */
    protected $validator;

    /**
     * PartnersController constructor.
     *
     * @param PartnerRepository $repository
     * @param PartnerValidator $validator
     */
    public function __construct(PartnerRepository $repository, PartnerValidator $validator)
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
        $partners = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $partners,
            ]);
        }

        return view('partners.index', compact('partners'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  PartnerCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(PartnerCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $partner = $this->repository->create($request->all());

            $response = [
                'message' => 'Partner created.',
                'data'    => $partner->toArray(),
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
        $partner = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $partner,
            ]);
        }

        return view('partners.show', compact('partner'));
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
        $partner = $this->repository->find($id);

        return view('partners.edit', compact('partner'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  PartnerUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(PartnerUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $partner = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Partner updated.',
                'data'    => $partner->toArray(),
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
                'message' => 'Partner deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Partner deleted.');
    }
}
