<?php

namespace App\Services;

use App\Entities\Cart;
use App\Entities\Order;
use App\Utils\Constants;
use Carbon\Carbon;
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
}
