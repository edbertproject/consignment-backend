<?php

namespace App\Services;

use App\Entities\ApiExternalLog;
use App\Entities\Cart;
use App\Entities\Order;
use App\Http\Requests\OrderCreateRequest;
use App\Utils\Constants;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Xendit\Cards;
use Xendit\VirtualAccounts;
use Xendit\Xendit;

class XenditService
{
    public static function createXendit(OrderCreateRequest $request, $invoice): array
    {
        Xendit::setApiKey(config('xendit.key'));
        $paymentMethod = $invoice->paymentMethod;

        if ($paymentMethod->type == Constants::PAYMENT_METHOD_TYPE_VIRTUAL_ACCOUNT) {

            $params = [
                'external_id' => $invoice->number,
                'bank_code' => $paymentMethod->xendit_code,
                'name' => Auth::user()->name,
                'is_closed' => true,
                'expected_amount' => $invoice->grand_total,
                'expiration_date' => Carbon::parse($invoice->expires_at)->toIso8601String(),
                'is_single_use' => false
            ];

            $xenditResponse = VirtualAccounts::create($params);

            $invoice->payment_number = Arr::get($xenditResponse, 'account_number');
        }
        else if ($paymentMethod->type == Constants::PAYMENT_METHOD_TYPE_CREDIT_CARD) {
            $params = [
                'token_id' => $request->get('xendit_token_id'),
                'external_id' => $invoice->number,
                'authentication_id' => $request->get('xendit_authentication_id'),
                'amount' => $invoice->grand_total,
                'card_cvn' => $request->get('xendit_card_cvn'),
                'capture' => true
            ];

            $xenditResponse = Cards::create($params);

            if (in_array(Arr::get($xenditResponse, 'status'), ['AUTHORIZED', 'CAPTURED'])) {
                InvoiceService::setPaid($invoice->id);
            }
        }

        if (empty($xenditResponse)) {
            throw new \Exception("The payment method is invalid.");
        }

        $invoice->xendit_key = Arr::get($xenditResponse, 'id');
        $invoice->save();

        return $xenditResponse;
    }

    public static function payXendit($invoice)
    {
        $fullUrl = config('xendit.base_url').'/callback_virtual_accounts/external_id=' . urlencode($invoice->number) . '/simulate_payment';

        $options = [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode(config('xendit.key') . ':'),
            ],
            'json' =>[
                'amount' => $invoice->grand_total
            ]
        ];

        try {
            $client = new Client();
            $response = $client->POST($fullUrl, $options);
            $rawResponse = $response->getBody()->__toString();
            $parsedResponse = json_decode($rawResponse);

//            InvoiceService::setPaid($invoice->id);
        } catch(ClientException $e) {
            $responseCode = $e->getResponse()->getStatusCode();
            $responseMessage = $e->getResponse()->getBody()->getContents();

            $parsedResponse = [
                'code' => $responseCode,
                'message' => $responseMessage
            ];
        }

        ApiExternalLog::create([
            'vendor' => 'XENDIT',
            'url' => $fullUrl,
            'request_header' => json_encode($options['headers']),
            'request_body' => json_encode($options['json']),
            'response' => json_encode($parsedResponse)
        ]);

        return $parsedResponse;
    }
}
