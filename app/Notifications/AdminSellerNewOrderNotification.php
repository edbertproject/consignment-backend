<?php

namespace App\Notifications;

use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class AdminSellerNewOrderNotification extends Notification
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
        return ['database'];
    }

    /**
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
