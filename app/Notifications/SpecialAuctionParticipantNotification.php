<?php

namespace App\Notifications;

use App\Services\ProductService;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SpecialAuctionParticipantNotification extends Notification
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
            ->subject('Congratulation you are selected for join special auction')
            ->greeting('Congratulations '.$notifiable->name)
            ->line('You are selected for participate in special auction at '.Carbon::parse($this->product->start_date)->format("d-m-Y hh:mm"))
            ->line('The product being auctioned is '. $this->product->name . ' with start price at '. $this->product->start_price)
            ->action('Link', ProductService::generateFrontendUrl($this->product))
            ->line('We will waiting you to participate in this auction');
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
            //
        ];
    }
}
