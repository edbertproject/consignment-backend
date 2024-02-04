<?php

namespace App\Services;

use App\Entities\Cart;
use App\Entities\Order;
use App\Entities\OrderStatus;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class OrderService
{
    public static function updateStatus($id, $status, $type = Constants::ORDER_STATUS_TYPE_PRIMARY) {
        if (empty($status)) return null;

        return OrderStatus::query()
            ->create([
                'order_id' => $id,
                'status' => $status,
                'type' => $type
            ]);
    }

    public static function updateAvailableCart($productId, $availableQuantity) {
        $carts = Cart::query()
            ->where('product_id', $productId)
            ->get();

        foreach($carts as $cart) {
            if ($availableQuantity === 0) {
                $cart->update([
                    'is_available' => false
                ]);

//                $cart->customer->user->notify(new CartUnavailableNotification());
            } else if ($cart->quantity > $availableQuantity) {
                $cart->update([
                    'quantity' => $availableQuantity
                ]);

//                $cart->customer->user->notify(new CartUnavailableNotification());
            }
            $cart->save();
        }

        return true;
    }

    public static function storeOrder(array $data, $orderedNumber)
    {
        $ordered = Order::query()
            ->create([
                'date' => $data['date'],
                'number' => $orderedNumber,
                'invoice_id' => $data['invoice_id'],
                'user_id' => $data['user_id'],
                'user_address_id' => $data['user_address_id'],
                'product_id' => $data['product_id'],
                'partner_id' => $data['partner_id'] ?? null,
                'quantity' => $data['quantity'],
                'price' => $data['price'],
            ]);

        static::updateStatus($ordered->id,Constants::ORDER_STATUS_WAITING_PAYMENT);

//        ScheduleService::updateStatus($ordered->schedule_id, Constant::SCHEDULE_STATUS_WAITING_FOR_PAYMENT);
//        ScheduleService::deleteSimilarSchedule($ordered->schedule_id);
//        BookingService::updateAvailableCart($ordered->schedule_id, 0);
    }

    public static function handleUpdateStatusSeller($statusSeller, $orderId) {
        $orderStatus = null;
        $orderBuyerStatus = null;

        switch ($statusSeller) {
            case Constants::ORDER_SELLER_STATUS_PROCESSING:
                $orderStatus = Constants::ORDER_STATUS_PROCESS;
                $orderBuyerStatus = Constants::ORDER_BUYER_STATUS_PROCESSED;
                break;
            case Constants::ORDER_SELLER_STATUS_ON_DELIVERY:
                $orderStatus = Constants::ORDER_STATUS_PROCESS;
                $orderBuyerStatus = Constants::ORDER_BUYER_STATUS_ON_DELIVERY;
                break;
            case Constants::ORDER_SELLER_STATUS_CANCELED:
                $orderStatus = Constants::ORDER_STATUS_CANCELED;
                $orderBuyerStatus = Constants::ORDER_BUYER_STATUS_CANCELED;
                break;
        }

        if (!empty($orderStatus)) {
            static::updateStatus($orderId,$orderStatus);
        }

        if (!empty($orderBuyerStatus)) {
            static::updateStatus($orderId,$orderBuyerStatus,Constants::ORDER_STATUS_TYPE_BUYER);
        }
    }

    public static function handleUpdateStatusBuyer($statusBuyer, $orderId) {
        $orderStatus = null;
        $orderSellerStatus = null;

        switch ($statusBuyer) {
            case Constants::ORDER_BUYER_STATUS_ARRIVED:
                $orderSellerStatus = Constants::ORDER_SELLER_STATUS_ARRIVED;
                break;
            case Constants::ORDER_BUYER_STATUS_COMPLAIN:
                $orderStatus = Constants::ORDER_STATUS_PROBLEM;
                $orderSellerStatus = Constants::ORDER_SELLER_STATUS_COMPLAIN;
                break;
            case Constants::ORDER_BUYER_STATUS_COMPLETE:
                $orderStatus = Constants::ORDER_STATUS_FINISH;
                $orderSellerStatus = Constants::ORDER_SELLER_STATUS_COMPLETE;
                break;
        }

        if (!empty($orderStatus)) {
            static::updateStatus($orderId,$orderStatus);
        }

        if (!empty($orderSellerStatus)) {
            static::updateStatus($orderId,$orderSellerStatus,Constants::ORDER_STATUS_TYPE_SELLER);
        }
    }

    public static function getNextStatus($order) {
        return [
            'status' => $order->status === Constants::ORDER_STATUS_PROBLEM ? Constants::ORDER_STATUS_FINISH : null,
            'can' => !Auth::user()->hasRole(Constants::ROLE_PARTNER_ID) && $order->status === Constants::ORDER_STATUS_PROBLEM
        ];
    }

    public static function getNextStatusSeller($order) {
        $can = !empty($order->partner_id) ? $order->partner_id === @Auth::user()->partner->id : !Auth::user()->hasRole(Constants::ROLE_PARTNER_ID);
        $status = null;

        switch ($order->status_seller) {
            case Constants::ORDER_SELLER_STATUS_WAITING_CONFIRM:
                $status = [Constants::ORDER_SELLER_STATUS_PROCESSING, Constants::ORDER_SELLER_STATUS_CANCELED];
                break;
            case Constants::ORDER_SELLER_STATUS_PROCESSING;
                $status = [Constants::ORDER_SELLER_STATUS_ON_DELIVERY, Constants::ORDER_SELLER_STATUS_CANCELED];
                break;
        }

        return [
            'status' => $status,
            'can' => $can
        ];
    }

    public static function getNextStatusBuyer($order) {
        $can = $order->user_id === Auth::id();
        $status = null;

        switch ($order->status_buyer) {
            case Constants::ORDER_BUYER_STATUS_ON_DELIVERY:
                $status = [Constants::ORDER_BUYER_STATUS_ARRIVED];
                break;
            case Constants::ORDER_BUYER_STATUS_ARRIVED;
                $status = [Constants::ORDER_BUYER_STATUS_COMPLAIN, Constants::ORDER_BUYER_STATUS_COMPLETE];
                break;
            case Constants::ORDER_BUYER_STATUS_COMPLAIN;
                $status = [Constants::ORDER_BUYER_STATUS_COMPLETE];
                break;
        }

        return [
            'status' => $status,
            'can' => $can
        ];
    }

    public static function checkAndUpdateExpiredStatus() {
        $cancelOrders = Order::query()
            ->leftJoin(DB::raw('LATERAL (
                SELECT order_statuses.status, order_statuses.updated_at, order_statuses.order_id
                FROM order_statuses
                WHERE order_statuses.order_id = orders.id
                AND order_statuses.type = "Seller"
                ORDER BY order_statuses.created_at DESC
                LIMIT 1
            ) AS last_statuses'),'last_statuses.order_id','orders.id')
            ->where(function ($where) {
                $where->where('last_statuses.status', Constants::ORDER_SELLER_STATUS_WAITING_CONFIRM)
                    ->where('last_statuses.updated_at','<', Carbon::now()->subHours(Constants::ORDER_SELLER_STATUS_WAITING_CONFIRM_EXPIRE));
            })->orWhere(function ($where) {
                $where->where('last_statuses.status', Constants::ORDER_SELLER_STATUS_PROCESSING)
                    ->where('last_statuses.updated_at','<', Carbon::now()->subHours(Constants::ORDER_SELLER_STATUS_PROCESSING_EXPIRE));
            })->get();

        foreach ($cancelOrders as $order) {
            static::updateStatus($order->id, Constants::ORDER_SELLER_STATUS_CANCELED, Constants::ORDER_STATUS_TYPE_SELLER);
            static::updateStatus($order->id, Constants::ORDER_BUYER_STATUS_CANCELED, Constants::ORDER_STATUS_TYPE_BUYER);
            static::updateStatus($order->id, Constants::ORDER_STATUS_CANCELED);
            // notification
        }

        $confirmedOrders = Order::query()
            ->leftJoin(DB::raw('LATERAL (
                SELECT order_statuses.status, order_statuses.updated_at, order_statuses.order_id
                FROM order_statuses
                WHERE order_statuses.order_id = orders.id
                AND order_statuses.type = "Buyer"
                ORDER BY order_statuses.created_at DESC
                LIMIT 1
            ) AS last_statuses'),'last_statuses.order_id','orders.id')
            ->where('last_statuses.status', Constants::ORDER_BUYER_STATUS_ARRIVED)
            ->where('last_statuses.updated_at','<', Carbon::now()->subHours(Constants::ORDER_BUYER_STATUS_ARRIVED_EXPIRE))
            ->get();

        foreach ($confirmedOrders as $order) {
            static::updateStatus($order->id, Constants::ORDER_SELLER_STATUS_COMPLETE, Constants::ORDER_STATUS_TYPE_SELLER);
            static::updateStatus($order->id, Constants::ORDER_BUYER_STATUS_COMPLETE, Constants::ORDER_STATUS_TYPE_BUYER);
            static::updateStatus($order->id, Constants::ORDER_STATUS_FINISH);
            // notification
        }
    }
}
