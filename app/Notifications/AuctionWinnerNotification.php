<?php

namespace App\Notifications;

use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionWinnerNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(protected $product)
    {
        //
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
        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'), 'Consignx')
            ->subject('Congratulations you win auction')
            ->greeting('Congratulations '.$notifiable->name)
            ->line('You win auction for product '.$this->product->name)
            ->line('Please checkout product immediately before '.Carbon::parse($this->product->end_date)->addHours(Constants::PRODUCT_AUCTION_CHECKOUT_EXPIRES)->format('Y-m-d H:i'));
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
            'subject' => 'Congratulations you win auction',
            'message' => 'You win auction for product '. $this->product->name .', please checkout product immediately before '.Carbon::parse($this->product->end_date)->addHours(Constants::PRODUCT_AUCTION_CHECKOUT_EXPIRES)->format('Y-m-d H:i')
        ];
    }
}
