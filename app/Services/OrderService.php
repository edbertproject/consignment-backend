<?php

namespace App\Services;

use App\Entities\Cart;
use App\Entities\Order;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;

class OrderService
{
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
                'status' => Constants::ORDER_STATUS_WAITING_PAYMENT
            ]);

//        ScheduleService::updateStatus($ordered->schedule_id, Constant::SCHEDULE_STATUS_WAITING_FOR_PAYMENT);
//        ScheduleService::deleteSimilarSchedule($ordered->schedule_id);
//        BookingService::updateAvailableCart($ordered->schedule_id, 0);
    }

    public static function handleUpdateStatusSeller($order) {
        switch ($order->status_seller) {
            case Constants::ORDER_SELLER_STATUS_PROCESSING:
                $order->status = Constants::ORDER_STATUS_PROCESS;
                $order->status_buyer = Constants::ORDER_BUYER_STATUS_PROCESSED;
                $order->status_buyer_updated_at = Carbon::now();
                break;
            case Constants::ORDER_SELLER_STATUS_ON_DELIVERY:
                $order->status = Constants::ORDER_STATUS_PROCESS;
                $order->status_buyer = Constants::ORDER_BUYER_STATUS_ON_DELIVERY;
                $order->status_buyer_updated_at = Carbon::now();
                break;
            case Constants::ORDER_SELLER_STATUS_CANCELED:
                $order->status = Constants::ORDER_STATUS_CANCELED;
                $order->status_buyer = Constants::ORDER_BUYER_STATUS_CANCELED;
                $order->status_buyer_updated_at = Carbon::now();
                break;
        }

        $order->status_seller_updated_at = Carbon::now();
        $order->save();
    }

    public static function handleUpdateStatusBuyer($order) {
        switch ($order->status_buyer) {
            case Constants::ORDER_BUYER_STATUS_ARRIVED:
                $order->status_seller = Constants::ORDER_SELLER_STATUS_ARRIVED;
                $order->status_seller_updated_at = Carbon::now();
                break;
            case Constants::ORDER_BUYER_STATUS_COMPLAIN:
                $order->status = Constants::ORDER_STATUS_PROBLEM;
                $order->status_seller = Constants::ORDER_SELLER_STATUS_COMPLAIN;
                $order->status_seller_updated_at = Carbon::now();
                break;
            case Constants::ORDER_BUYER_STATUS_COMPLETE:
                $order->status = Constants::ORDER_STATUS_FINISH;
                $order->status_seller = Constants::ORDER_SELLER_STATUS_COMPLETE;
                $order->status_seller_updated_at = Carbon::now();
                break;
        }

        $order->status_buyer_updated_at = Carbon::now();
        $order->save();
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
            ->where(function ($where) {
                $where->where('status_seller', Constants::ORDER_SELLER_STATUS_WAITING_CONFIRM)
                    ->where('status_seller_updated_at','<', Carbon::now()->subHours(Constants::ORDER_SELLER_STATUS_WAITING_CONFIRM_EXPIRE));
            })->orWhere(function ($where) {
                $where->where('status_seller', Constants::ORDER_SELLER_STATUS_PROCESSING)
                    ->where('status_seller_updated_at','<', Carbon::now()->subHours(Constants::ORDER_SELLER_STATUS_PROCESSING_EXPIRE));
            })->get();

        foreach ($cancelOrders as $order) {
            $order->status_seller = Constants::ORDER_SELLER_STATUS_CANCELED;
            $order->status_buyer = Constants::ORDER_BUYER_STATUS_CANCELED;
            $order->status = Constants::ORDER_STATUS_CANCELED;
            $order->save();

            // notification
        }

        $confirmedOrders = Order::query()
            ->where('status_buyer', Constants::ORDER_BUYER_STATUS_ARRIVED)
            ->where('status_buyer_updated_at','<', Carbon::now()->subHours(Constants::ORDER_BUYER_STATUS_ARRIVED_EXPIRE))
            ->get();

        foreach ($confirmedOrders as $order) {
            $order->status_buyer = Constants::ORDER_BUYER_STATUS_COMPLETE;
            $order->status_seller = Constants::ORDER_SELLER_STATUS_COMPLETE;
            $order->status = Constants::ORDER_STATUS_FINISH;
            $order->save();

            // notification
        }
    }
}
