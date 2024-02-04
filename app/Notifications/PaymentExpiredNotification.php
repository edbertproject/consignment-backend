<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentExpiredNotification extends Notification
{
    use Queueable;

    protected $invoice;
    protected $orderNumber;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($invoice,$orderNumber)
    {
        $this->invoice = $invoice;
        $this->orderNumber = $orderNumber;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database','mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'), 'Consignx')
            ->subject('Payment Expired Notification #'.$this->invoice->number)
            ->greeting('Hello, '.$notifiable->name)
            ->line('Your order ('.implode(', ',$this->orderNumber).') has been automatically canceled because the payment deadline has passed.')
            ->line('Thank you!');
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
            'subject' => 'Payment Expired Notification #'.$this->invoice->number,
            'message' => 'Your order ('.implode(', ',$this->orderNumber).') has been automatically canceled because the payment deadline has passed.'
        ];
    }
}
