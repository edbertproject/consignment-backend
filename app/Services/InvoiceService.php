<?php

namespace App\Services;

use App\Entities\Invoice;
use App\Entities\Order;
use App\Entities\User;
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

            $order->status = Constants::ORDER_STATUS_EXPIRED;
            $order->save();

            $orderNumber[] = $order->number;
        }

//        $user = User::find($invoice->user_id);
//        $user->notify(new PaymentExpiredNotification($invoice,$orderNumber));
    }

    public static function setPaid($invoiceId)
    {
        $invoice = Invoice::query()->find($invoiceId);

        if(!empty($invoice)) {
            $invoice->status = Constants::INVOICE_STATUS_PAID;
            $invoice->save();
        }

        foreach($invoice->orders as $order) {
            $order->status = Constants::ORDER_STATUS_PAID;
            $order->save();

            $availableQuantity = $order->product->available_quantity - $order->quantity;

            ProductService::updateAvailableQuantity($order->product, $availableQuantity);
            OrderService::updateAvailableCart($order->product_id, $availableQuantity);
        }

//        foreach ($bookingNotifications as $bookingNotification) {
//            $bookingNotification->schedule->psychologist->user->notify(new PsychologistNewCustomerNotification($bookingNotification));
//
//            UserService::sendNewBookingAdminNotification($bookingNotification);
//        }

//        $user = User::find($invoice->user_id);
//        $user->notify(new PaymentPaidNotification($invoice));

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
