<?php

namespace App\Http\Controllers;

use App\Entities\Invoice;
use App\Http\Requests\XenditInvoiceRequest;
use App\Http\Requests\XenditVirtualAccountRequest;
use App\Http\Requests\XenditVirtualAccountUpdateRequest;
use App\Services\InvoiceService;
use App\Utils\Constants;
use Illuminate\Http\Request;

class XenditController extends Controller
{
    public function invoice(XenditInvoiceRequest $request)
    {
        /* xendit test mode */
        if ($request->get('external_id') == 'invoice_123124123') {
            return response()->json([
                'success' => true
            ]);
        }
        /* end xendit test mode */

        $invoice = Invoice::where('number', $request->get('external_id'))->first();
        if (empty($invoice)) {
            return response()->json([
                'success' => true
            ]);
        }

        switch ($request->get('status')) {
            case Constants::XENDIT_INVOICE_STATUS_PAID:
                InvoiceService::setPaid($invoice->id);
                break;
            case Constants::XENDIT_INVOICE_STATUS_EXPIRED:
                if ($invoice->status == Constants::INVOICE_STATUS_PENDING) {
                    InvoiceService::setExpired($invoice->id);
                }
                break;
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function virtualAccount(XenditVirtualAccountRequest $request)
    {
        /* xendit test mode */
        if ($request->get('external_id') == 'fixed-va-1487156410') {
            return response()->json([
                'success' => true
            ]);
        }
        /* end xendit test mode */

        $invoice = Invoice::where('number', $request->get('external_id'))->first();
        if (empty($invoice)) {
            return response()->json([
                'success' => true
            ]);
        }

        if ($invoice->grand_total == $request->get('amount')) {
            InvoiceService::setPaid($invoice->id);
            return response()->json([
                'success' => true,
                'data' => $invoice,
            ]);
        }

        return abort(404);
    }

    public function virtualAccountUpdate(XenditVirtualAccountUpdateRequest $request)
    {
        /* xendit test mode */
        if ($request->get('external_id') == 'fixed-va-1487156410') {
            return response()->json([
                'success' => true
            ]);
        }
        /* end xendit test mode */

        $invoice = Invoice::where('number', $request->get('external_id'))->first();
        if (empty($invoice)) {
            return response()->json([
                'success' => true
            ]);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
