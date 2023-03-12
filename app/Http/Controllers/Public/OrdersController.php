<?php

namespace App\Http\Controllers\Public;

use App\Entities\ApiExternalLog;
use App\Entities\Cart;
use App\Entities\Invoice;
use App\Entities\Order;
use App\Entities\PaymentMethod;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Services\InvoiceService;
use App\Services\NumberSettingService;
use App\Services\OrderService;
use App\Utils\Constants;
use App\Utils\Traits\RestControllerTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\OrderCreateRequest;
use App\Repositories\OrderRepository;
use Xendit\Cards;
use Xendit\VirtualAccounts;
use Xendit\Xendit;

/**
 * Class OrdersController.
 *
 * @package namespace App\Http\Controllers;
 */
class OrdersController extends Controller
{
    use RestControllerTrait {
        RestControllerTrait::__construct as public __rest;
    }

    public function __construct(OrderRepository $repository)
    {
        $this->__rest($repository);
    }

    public function store(OrderCreateRequest $request)
    {
        $user = $request->user();
        $cartIds = collect($request->get('cart_ids', []));
        $paymentMethod = PaymentMethod::query()->find($request->get('payment_method_id'));

        $date = Carbon::now('Asia/Jakarta')->toDateString();

        $invoiceNumber = NumberSettingService::generate(Invoice::class);
        $orderedNumber = [];
        $totalOrder = $cartIds->count();
        for ($i = 0; $i < $totalOrder; $i++) {
            $orderedNumber[] = NumberSettingService::generate(Order::class);
        }

        try {
            DB::beginTransaction();

            $invoice = Invoice::query()
                ->create([
                    'user_id' => $user->id,
                    'date' => $date,
                    'number' => $invoiceNumber,
                    'expires_at' => now()->addMinutes(Constants::INVOICE_EXPIRES),
                    'status' => Constants::INVOICE_STATUS_PENDING,
                    'payment_method_id' => $paymentMethod->id,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'admin_fee' => 0,
                    'platform_fee' => 0,
                    'grand_total' => 0,
                ]);

            $index = 0;
            foreach ($cartIds as $cartId) {
                $cart = Cart::query()->findOrFail($cartId);
                $invoice->subtotal += $cart->product->price * $cart->quantity;

                $data = [
                    'date' => $date,
                    'invoice_id' => $invoice->id,
                    'user_id' => $user->id,
                    'user_address_id' => $request->get('user_address_id'),
                    'product_id' => $cart->product->id,
                    'partner_id' => $cart->product->partner_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->product->price,
                ];

                OrderService::storeOrder($data, $orderedNumber[$index]);
                $index++;

                $cart->delete();
            }

            $invoice->tax_amount = /*(int) (($invoice->subtotal * 11) / 100)*/ 0;
            $invoice->admin_fee = InvoiceService::getAdminFee($paymentMethod->type, $invoice->subtotal);
            $invoice->platform_fee = (int) (($invoice->subtotal * Constants::INVOICE_FEE_PLATFORM_AMOUNT_PERCENTAGE) / 100);
            $invoice->grand_total = $invoice->subtotal + $invoice->tax_amount + $invoice->admin_fee + $invoice->platform_fee;
            $invoice->save();

            $this->createXendit($request, $invoice);
//            $user->notify(new PaymentMethodNotification($invoice));

            DB::commit();

            if(config('app.env') != 'production') {
                sleep(5);
                $this->payXendit($invoice);
            }

            return ($this->showStore($request, $invoice->id))->additional([
                'success' => true,
                'message' => 'Data created.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function createXendit(OrderCreateRequest $request, $invoice)
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
        $fullUrl = 'https://api.xendit.co/callback_virtual_accounts/external_id=' . urlencode($invoice->number) . '/simulate_payment';

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

    protected function showStore(Request $request, $id)
    {
        $data = Invoice::query()
            ->with(['paymentMethod.logo', 'paymentMethod.paymentMethodInstructions'])
            ->select('invoices.*')
            ->addSelect('users.name AS customer_name')
            ->addSelect('products.status AS status_name')
            ->leftJoin('users', 'users.id', 'invoices.user_id')
            ->leftJoin('orders', 'orders.invoice_id', 'invoices.id')
            ->leftJoin('products', 'products.id', 'orders.product_id')
            ->where('invoices.id', $id)
            ->whereNull('invoices.deleted_at')
            ->first();

        return new BaseResource($data);
    }
}
