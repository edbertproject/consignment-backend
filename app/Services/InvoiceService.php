<?php

namespace App\Services;

use App\Entities\Invoice;
use App\Entities\Order;
use App\Entities\User;
use App\Notifications\AdminSellerNewOrderNotification;
use App\Notifications\PartnerSellerNewOrderNotification;
use App\Notifications\PaymentExpiredNotification;
use App\Notifications\PaymentPaidNotification;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\Queue;

class InvoiceService
{
    public static function checkInvoiceExpired()
    {
        $invoices = Invoice::query()
            ->where('invoices.expires_at', '<', Carbon::now())
            ->where('status', Constants::INVOICE_STATUS_PENDING)
            ->get();

        foreach ($invoices as $invoice) {
            static::setExpired($invoice->id);
        }
    }

    public static function setExpired($invoiceId)
    {
        $invoice = Invoice::query()->find($invoiceId);

        $invoice->status = Constants::INVOICE_STATUS_CANCELED;
        $invoice->save();

        $orderNumber = [];

        foreach ($invoice->orders as $order) {
            $order = Order::query()->findOrFail($order->schedule_id);

            OrderService::updateStatus($order->id, Constants::ORDER_STATUS_EXPIRED);

            $orderNumber[] = $order->number;
        }

        $user = User::find($invoice->user_id);
        $user->notify(new PaymentExpiredNotification($invoice,$orderNumber));
    }

    public static function setPaid($invoiceId)
    {
        $invoice = Invoice::query()->find($invoiceId);

        if(!empty($invoice)) {
            $invoice->status = Constants::INVOICE_STATUS_PAID;
            $invoice->save();
        }

        foreach($invoice->orders as $order) {
            OrderService::updateStatus($order->id, Constants::ORDER_STATUS_PAID);
            OrderService::updateStatus($order->id, Constants::ORDER_SELLER_STATUS_WAITING_CONFIRM,Constants::ORDER_STATUS_TYPE_SELLER);
            OrderService::updateStatus($order->id, Constants::ORDER_BUYER_STATUS_PENDING,Constants::ORDER_STATUS_TYPE_BUYER);

            $availableQuantity = $order->product->available_quantity - $order->quantity;

            ProductService::updateAvailableQuantity($order->product, $availableQuantity);
            OrderService::updateAvailableCart($order->product_id, $availableQuantity);

            $lastSellerStatus = @$order->sellerStatuses()->first()->updated_at;

            $expireStatusAt = Carbon::parse($lastSellerStatus ?? now())
                ->addHours(Constants::ORDER_SELLER_STATUS_WAITING_CONFIRM_EXPIRE);

            if (!empty($order->product->partner_id)) {
                $order->product->partner->user->notify(new PartnerSellerNewOrderNotification($order, $expireStatusAt));
            } else {
                $users = User::query()
                    ->whereHas('roles', function ($query) {
                        $query->whereNotIn('role_id', [
                            Constants::ROLE_PUBLIC_ID,
                            Constants::ROLE_PARTNER_ID,
                        ]);
                    })->get();

                foreach ($users as $user) {
                    $user->notify(new AdminSellerNewOrderNotification($order, $expireStatusAt));
                }
            }
        }

        $user = User::find($invoice->user_id);
        $user->notify(new PaymentPaidNotification($invoice));

        return true;
    }

    public static function getAdminFee($paymentMethod, $subtotal)
    {
        if($paymentMethod == Constants::PAYMENT_METHOD_TYPE_VIRTUAL_ACCOUNT) {
            $adminFee = Constants::XENDIT_FEE_VIRTUAL_ACCOUNT_AMOUNT;
        } else {
            $adminFee = (($subtotal * Constants::XENDIT_FEE_CREDIT_CARD_PERCENTAGE) / 100) + Constants::XENDIT_FEE_CREDIT_CARD_AMOUNT;
        }

        $adminFee = round($adminFee);

        return (int) $adminFee;
    }
}
