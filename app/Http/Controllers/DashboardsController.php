<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\Product;
use App\Entities\ProductBid;
use App\Entities\User;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OrdersController.
 *
 * @package namespace App\Http\Controllers;
 */
class DashboardsController extends Controller
{
    public function __construct()
    {
    }

    public function getSalesAccumulation(Request $request) {
        $isPartner = !empty(Auth::user()->partner);

        $prevMonth = !empty($request->get('prev')) ? Carbon::parse($request->get('prev'))->format('Y-m') : Carbon::now()->subMonth()->format('Y-m');
        $currentMonth = !empty($request->get('current')) ? Carbon::parse($request->get('current'))->format('Y-m') : Carbon::now()->format('Y-m');

        $baseQuery = Order::query()
            ->join('invoices','invoices.id','orders.invoice_id')
            ->whereHas('statuses', function ($status) {
                $status->where('order_statuses.status', Constants::ORDER_STATUS_FINISH);
            })->whereNull('orders.deleted_at')
            ->when($isPartner, function ($partner) {
                $partner->where('orders.partner_id', Auth::user()->partner->id);
            });

        $currentMonthRes = $baseQuery->clone()
            ->whereRaw('DATE_FORMAT(invoices.date, "%Y-%m") = ?', [$currentMonth])
            ->sum('invoices.grand_total');

        $prevMonthRes = $baseQuery->clone()
            ->whereRaw('DATE_FORMAT(invoices.date, "%Y-%m") = ?', [$prevMonth])
            ->sum('invoices.grand_total');

        return response()->json([
            'success' => true,
            'data' => [
                'prev_month' => $prevMonthRes,
                'current_month' => $currentMonthRes,
                'comparison_percentage' => static::calculatePercentage($prevMonthRes,$currentMonthRes)
            ]
        ]);
    }

    public function getBidAccumulation(Request $request) {
        $isPartner = !empty(Auth::user()->partner);

        $prevMonth = !empty($request->get('prev')) ? Carbon::parse($request->get('prev'))->format('Y-m') : Carbon::now()->subMonth()->format('Y-m');
        $currentMonth = !empty($request->get('current')) ? Carbon::parse($request->get('current'))->format('Y-m') : Carbon::now()->format('Y-m');

        $baseQuery = Product::query()
            ->select(
                'products.id AS product_id',
                DB::raw('IFNULL(MAX(product_bids.amount),1) AS max_auction_amount'),
            )->join('product_bids','product_bids.product_id','products.id')
            ->whereNull('products.deleted_at')
            ->when($isPartner, function ($partner) {
                $partner->where('products.partner_id',Auth::user()->partner->id);
            })->groupBy('product_bids.product_id');

        $currentMonthRes = $baseQuery->clone()
            ->whereRaw('DATE_FORMAT(products.start_date, "%Y-%m") = ?', [$currentMonth])
            ->get()->sum('max_auction_amount');

        $prevMonthRes = $baseQuery->clone()
            ->whereRaw('DATE_FORMAT(products.start_date, "%Y-%m") = ?', [$prevMonth])
            ->get()->sum('max_auction_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'prev_month' => $prevMonthRes,
                'current_month' => $currentMonthRes,
                'comparison_percentage' => static::calculatePercentage($prevMonthRes,$currentMonthRes)
            ]
        ]);
    }

    public function getProductPosting(Request $request) {
        $isPartner = !empty(Auth::user()->partner);

        $prevMonth = !empty($request->get('prev')) ? Carbon::parse($request->get('prev'))->format('Y-m') : Carbon::now()->subMonth()->format('Y-m');
        $currentMonth = !empty($request->get('current')) ? Carbon::parse($request->get('current'))->format('Y-m') : Carbon::now()->format('Y-m');

        $baseQuery = Product::query()
            ->whereNull('products.deleted_at')
            ->whereNotIn('status', [
                Constants::PRODUCT_STATUS_REJECTED,
                Constants::PRODUCT_STATUS_CANCEL_APPROVED,
                Constants::PRODUCT_STATUS_WAITING_APPROVAL
            ])->when($isPartner, function ($partner) {
                $partner->where('products.partner_id',Auth::user()->partner->id);
            });

        $currentMonthRes = $baseQuery->clone()
            ->whereRaw('DATE_FORMAT(products.start_date, "%Y-%m") = ?', [$currentMonth])
            ->count('products.id');

        $prevMonthRes = $baseQuery->clone()
            ->whereRaw('DATE_FORMAT(products.start_date, "%Y-%m") = ?', [$prevMonth])
            ->count('products.id');

        return response()->json([
            'success' => true,
            'data' => [
                'prev_month' => $prevMonthRes,
                'current_month' => $currentMonthRes,
                'comparison_percentage' => static::calculatePercentage($prevMonthRes,$currentMonthRes)
            ]
        ]);
    }

    public function getUserRegister(Request $request) {
        $prevMonth = !empty($request->get('prev')) ? Carbon::parse($request->get('prev'))->format('Y-m') : Carbon::now()->subMonth()->format('Y-m');
        $currentMonth = !empty($request->get('current')) ? Carbon::parse($request->get('current'))->format('Y-m') : Carbon::now()->format('Y-m');

        $baseQuery = User::query()
            ->where('is_active', true)
            ->whereNull('deleted_at');

        $currentMonthRes = $baseQuery->clone()
            ->whereRaw('DATE_FORMAT(users.created_at, "%Y-%m") = ?', [$currentMonth])
            ->count('users.id');

        $prevMonthRes = $baseQuery->clone()
            ->whereRaw('DATE_FORMAT(users.created_at, "%Y-%m") = ?', [$prevMonth])
            ->count('users.id');

        return response()->json([
            'success' => true,
            'data' => [
                'prev_month' => $prevMonthRes,
                'current_month' => $currentMonthRes,
                'comparison_percentage' => static::calculatePercentage($prevMonthRes,$currentMonthRes)
            ]
        ]);
    }

    public function getPendingOrder(Request $request) {
        $isPartner = !empty(Auth::user()->partner);

        $pendingOrders = Order::query()
            ->with(['product'])
            ->leftJoin(DB::raw('LATERAL (
                SELECT order_statuses.status, order_statuses.order_id
                FROM order_statuses
                WHERE order_statuses.order_id = orders.id
                AND order_statuses.type = "Primary"
                ORDER BY order_statuses.created_at  DESC
                LIMIT 1
            ) AS last_statuses'),'last_statuses.order_id','orders.id')
            ->whereIn('last_statuses.status', [
                Constants::ORDER_STATUS_PAID,
                Constants::ORDER_STATUS_PROCESS,
                Constants::ORDER_STATUS_PROBLEM,
            ])->whereNull('orders.deleted_at')
            ->when($isPartner, function ($partner) {
                $partner->where('orders.partner_id', Auth::user()->partner->id);
            })->orderByDesc('orders.created_at')
            ->limit(5)->get();

        return response()->json([
            'success' => true,
            'data' => $pendingOrders
        ]);
    }

    static private function calculatePercentage($prev, $current) {
        return round((($current - $prev) / ($prev == 0 ? 1 : $prev)) * 100, 2);
    }
}
