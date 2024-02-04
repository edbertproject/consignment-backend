<?php

namespace App\Notifications;

use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class PartnerSellerNewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;
    protected $expired;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order, $expired)
    {
        $this->order = $order;
        $this->expired = $expired;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $header = NotificationService::getNewOrderHeader();
        $table = NotificationService::getNewOrderTable($this->order);
        $note = NotificationService::getNewOrderNote($this->expired);

        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'), 'Consignx')
            ->subject('New Order #'.$this->order->number)
            ->line(new HtmlString($header))
            ->line(new HtmlString($table))
            ->line('Things that you need to do:')
            ->line(new HtmlString($note))
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
            'subject' => 'New Order #'.$this->order->number,
            'message' => 'You have a new order, please confirm order before ' . Carbon::parse($this->expired)->format('d F Y, H:i:s')
        ];
    }
}
