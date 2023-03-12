<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\InvoiceCreateRequest;
use App\Http\Requests\InvoiceUpdateRequest;
use App\Repositories\InvoiceRepository;
use App\Validators\InvoiceValidator;

/**
 * Class InvoicesController.
 *
 * @package namespace App\Http\Controllers;
 */
class InvoicesController extends Controller
{
    /**
     * @var InvoiceRepository
     */
    protected $repository;

    /**
     * @var InvoiceValidator
     */
    protected $validator;

    /**
     * InvoicesController constructor.
     *
     * @param InvoiceRepository $repository
     * @param InvoiceValidator $validator
     */
    public function __construct(InvoiceRepository $repository, InvoiceValidator $validator)
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
        $invoices = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $invoices,
            ]);
        }

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  InvoiceCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(InvoiceCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $invoice = $this->repository->create($request->all());

            $response = [
                'message' => 'Invoice created.',
                'data'    => $invoice->toArray(),
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
        $invoice = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $invoice,
            ]);
        }

        return view('invoices.show', compact('invoice'));
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
        $invoice = $this->repository->find($id);

        return view('invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  InvoiceUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(InvoiceUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $invoice = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Invoice updated.',
                'data'    => $invoice->toArray(),
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
                'message' => 'Invoice deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Invoice deleted.');
    }
}
