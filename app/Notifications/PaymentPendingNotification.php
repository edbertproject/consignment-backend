<?php

namespace App\Notifications;

use App\Services\NotificationService;
use App\Utils\Constants;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class PaymentPendingNotification extends Notification
{
    use Queueable;

    protected $invoice;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $header = NotificationService::getPendingPaymentHeader($this->invoice);
        $table = NotificationService::getPendingPaymentTable($this->invoice);
        $footer = NotificationService::getGeneralFooter();

        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'), 'Consignx')
            ->subject('Pending Payment Notification #'.$this->invoice->number)
            ->greeting('Hi '.$notifiable->name)
            ->line(new HtmlString($header))
            ->line('Below are your payment details')
            ->line(new HtmlString($table))
            ->line(new HtmlString($footer));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'subject' => 'Pending Payment Notification #'.$this->invoice->number,
            'message' => 'Thank you for your order! Please complete your payment within '. Constants::INVOICE_EXPIRES .' minutes, or your order will be automatically canceled. Check your email for the payment details.'
        ];
    }
}
