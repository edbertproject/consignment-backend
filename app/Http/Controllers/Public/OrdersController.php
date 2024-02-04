<?php

namespace App\Http\Controllers\Public;

use App\Criteria\Public\OrderCriteria;
use App\Entities\ApiExternalLog;
use App\Entities\Cart;
use App\Entities\Invoice;
use App\Entities\Order;
use App\Entities\PaymentMethod;
use App\Entities\Product;
use App\Http\Controllers\Controller;
use App\Http\Resources\BaseResource;
use App\Notifications\PaymentPendingNotification;
use App\Services\ExceptionService;
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

        $this->indexCriterias = [
            OrderCriteria::class
        ];
    }

    public function check(Request $request) {
        $carts = Cart::query()
            ->select(
                DB::raw('IFNULL(products.partner_id,"ADMIN") AS partner')
            )->join('products','products.id','carts.product_id')
            ->where('carts.user_id',Auth::id())
            ->pluck('partner')
            ->all();

        if (count(array_unique($carts)) > 1) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout unavailable because product has different seller.'
            ], 422);
        }

        $currentCarts = Cart::query()
            ->where('user_id', Auth::id())
            ->get();

        foreach ($currentCarts as $cart) {
            if (Order::query()
                ->whereHas('invoice', function ($invoice) {
                    $invoice->where('status', Constants::INVOICE_STATUS_PENDING);
                })->where('product_id', $cart->product_id)
                ->where('user_id', Auth::id())
                ->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Checkout unavailable because product already checkout, please complete your order payment.'
                ], 422);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Checkout available.'
        ]);
    }

    public function checkAuction(Request $request) {
        $product = Product::query()
            ->with('photo')
            ->where('id',$request->product_id)
            ->where('status', Constants::PRODUCT_STATUS_CLOSED)
            ->whereRaw("DATE_ADD(products.end_date, INTERVAL ".Constants::PRODUCT_AUCTION_CHECKOUT_EXPIRES." HOUR) >= NOW()")
            ->where(function ($where) {
                $where->whereHas('bids', function ($bid) {
                    $bid->where('user_id',Auth::id());
                })->orWhere('winner_id',Auth::id());
            })->first();

        if (Order::query()
            ->whereHas('invoice', function ($invoice) {
                $invoice->where('status', Constants::INVOICE_STATUS_PENDING);
            })->where('product_id', $product->id)
            ->where('user_id', Auth::id())
            ->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout unavailable because product already checkout, please complete your order payment.'
            ], 422);
        }

        if (empty($product)) {
            return response()->json([
                'success' => false,
                'message' => 'Checkout for this product is unavailable.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Checkout available.',
            'data' => $product
        ]);
    }

    public function store(OrderCreateRequest $request)
    {
        $user = $request->user();
        $cartIds = collect($request->get('cart_ids', []));
        $productAuctionId = Product::find($request->get('product_auction_id'));
        $paymentMethod = PaymentMethod::query()->find($request->get('payment_method_id'));
        $date = Carbon::now('Asia/Jakarta')->toDateString();

        try {
            DB::beginTransaction();

            $invoice = Invoice::query()
                ->create([
                    'user_id' => $user->id,
                    'date' => $date,
                    'number' => NumberSettingService::generate(Invoice::class),
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

            if (count($cartIds) > 0) {
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

                    OrderService::storeOrder($data, NumberSettingService::generate(Order::class));

                    $cart->delete();
                }
            } else {
                $price = @$productAuctionId->bids()
                    ->where('user_id',Auth::id())
                    ->orderByDesc('id')
                    ->first()->amount;
                $invoice->subtotal += $price * $productAuctionId->available_quantity;

                $data = [
                    'date' => $date,
                    'invoice_id' => $invoice->id,
                    'user_id' => $user->id,
                    'user_address_id' => $request->get('user_address_id'),
                    'product_id' => $productAuctionId->id,
                    'partner_id' => $productAuctionId->partner_id,
                    'quantity' => $productAuctionId->available_quantity,
                    'price' => $price,
                ];

                OrderService::storeOrder($data, NumberSettingService::generate(Order::class));
            }

            $invoice->tax_amount = /*(int) (($invoice->subtotal * 11) / 100)*/ 0;
            $invoice->admin_fee = InvoiceService::getAdminFee($paymentMethod->type, $invoice->subtotal);
            $invoice->platform_fee = (int) (($invoice->subtotal * Constants::INVOICE_FEE_PLATFORM_AMOUNT_PERCENTAGE) / 100);
            $invoice->grand_total = $invoice->subtotal + $invoice->tax_amount + $invoice->admin_fee + $invoice->platform_fee + $invoice->courier_cost;
            $invoice->save();

            XenditService::createXendit($request, $invoice);
            $user->notify(new PaymentPendingNotification($invoice));

            DB::commit();

//            if(config('app.env') != 'production') {
//                sleep(2);
//                XenditService::payXendit($invoice);
//            }

            return ($this->showStore($request, $invoice->id))->additional([
                'success' => true,
                'message' => 'Data created.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateStatusBuyer(Requests\Public\OrderUpdateStatusBuyerRequest $request, string $id) {
        try {
            DB::beginTransaction();

            $orderStatus = OrderService::updateStatus($id,$request->get('status'),Constants::ORDER_STATUS_TYPE_BUYER);

            OrderService::handleUpdateStatusBuyer($orderStatus->status, $id);

            DB::commit();

            return ($this->show($request, $id))->additional([
                'success' => true,
                'message' => 'Data status updated.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return ExceptionService::responseJson($e);
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
