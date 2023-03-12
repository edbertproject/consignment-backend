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
use App\Services\XenditService;
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
                    'courier_code' => $request->get('courier_code'),
                    'courier_service' => $request->get('courier_service'),
                    'courier_esd' => $request->get('courier_esd'),
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'admin_fee' => 0,
                    'platform_fee' => 0,
                    'courier_cost' => $request->get('courier_cost'),
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
            $invoice->grand_total = $invoice->subtotal + $invoice->tax_amount + $invoice->admin_fee + $invoice->platform_fee + $invoice->courier_cost;
            $invoice->save();

            XenditService::createXendit($request, $invoice);
//            $user->notify(new PaymentMethodNotification($invoice));

            DB::commit();

            if(config('app.env') != 'production') {
                sleep(2);
                XenditService::payXendit($invoice);
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
