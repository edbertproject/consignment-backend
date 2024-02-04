<?php

namespace App\Services;

use Carbon\Carbon;

class NotificationService
{
    public static function getGeneralFooter()
    {
        return "If you have other inquiries, please contact us at <b>contact@consignx.com</b> or <b>+62 21 9999 9999</b>";
    }

    public static function getPendingPaymentHeader($invoice)
    {
        return "Thank you for your order! Please complete your payment before <b>" . Carbon::parse($invoice->expires_at)->format('d F Y, H:i:s') . "</b>, or your order will be automatically canceled.";
    }

    public static function getPendingPaymentTable($invoice)
    {
        return "<strong>
                    <table>
                        <tr>
                            <td class='text-left'>Invoice Number</td>
                            <td class='text-left'>:</td>
                            <td class='text-left'>" . $invoice->number . "</td>
                        </tr>
                        <tr>
                            <td class='text-left'>Order Date & Time</td>
                            <td class='text-left'>:</td>
                            <td class='text-left'>" . Carbon::parse($invoice->created_at)->format('d F Y, H:i:s') . "</td>
                        </tr>
                        <tr>
                            <td class='text-left'>Total Amount</td>
                            <td class='text-left'>:</td>
                            <td class='text-left'>Rp " . number_format($invoice->grand_total, 0, ',', '.') . "</td>
                        </tr>
                        <tr>
                            <td class='text-left'>Payment Method</td>
                            <td class='text-left'>:</td>
                            <td class='text-left'>" . $invoice->paymentMethod->name . "</td>
                        </tr>
                        <tr>
                            <td class='text-left'>Virtual Account No.</td>
                            <td class='text-left'>:</td>
                            <td class='text-left'>" . $invoice->payment_number . "</td>
                        </tr>
                    </table>
                </strong>";
    }

    public static function getPaidPaymentHeader($invoice)
    {
        return "Thank you, we have received your payment for invoice number <b>" . $invoice->number . "</b>.";
    }

    public static function getPaidPaymentTable($orders)
    {
        $table = "<table>";

        $number = 1;
        foreach($orders as $order) {
            $table .= "<tr>
                                <td class='number-table text-left text-bold'>" . $number++ . ".</td>
                                <td class='number-table text-left text-bold'>Product</td>
                                <td class='text-left text-bold'>:</td>
                                <td class='text-left text-bold'>" . $order->product->name . "</td>
                            </tr>
                            <tr>
                                <td class='text-left'></td>
                                <td class='text-left text-bold'>Quantity</td>
                                <td class='text-left text-bold'>:</td>
                                <td class='text-left text-bold'>" . $order->quantity . "pcs</td>
                            </tr>";


            $table .= "<tr>
                                <td class='text-left' colspan='4'><hr style='margin-bottom: 5px'></td>
                            </tr>";
        }

        $table .= "</table>";

        return $table;
    }

    public static function getOrderNote()
    {
        return "Please note that paid order is <u>non-cancellable and non-refundable</u>.";
    }

    public static function getNewOrderHeader()
    {
        return "You have a new order!";
    }

    public static function getNewOrderTable($order)
    {
        $table = "<table>";

        $number = 1;
        $table .= "<tr>
                                <td class='number-table text-left text-bold'>" . $number++ . ".</td>
                                <td class='number-table text-left text-bold'>Product</td>
                                <td class='text-left text-bold'>:</td>
                                <td class='text-left text-bold'>" . $order->product->name . "</td>
                            </tr>
                            <tr>
                                <td class='text-left'></td>
                                <td class='text-left text-bold'>Price</td>
                                <td class='text-left text-bold'>:</td>
                                <td class='text-left text-bold'>" . $order->product->price . "</td>
                            </tr>";


        $table .= "<tr>
            <td class='text-left' colspan='4'><hr style='margin-bottom: 5px'></td>
        </tr>";
        $table .= "</table>";

        return $table;
    }

    public static function getNewOrderNote($expired)
    {
        return "<table class='new-customer-row'>
                    <tr>
                        <td class='new-customer-note'>1.</td>
                        <td>Login to your account in seller area → see the Order menu</td>
                    </tr>
                    <tr>
                        <td class='new-customer-note'>2.</td>
                        <td>Check the order details</td>
                    </tr>
                    <tr>
                        <td class='new-customer-note'>3.</td>
                        <td>Please confirm your order before <strong>". Carbon::parse($expired)->format('d F Y, H:i:s') ."</strong></td>
                    </tr>
                    <tr>
                        <td class='new-customer-note'>4.</td>
                        <td>Don’t forget to <b>update status</b> periodically</td>
                    </tr>
                </table>";
    }

}
