<?php

namespace App\Notifications;

use App\Utilities\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class PaymentPaidNotification extends Notification implements ShouldQueue
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
        $header = NotificationService::getPaidPaymentHeader($this->invoice);
        $table = NotificationService::getPaidPaymentTable($this->invoice->orders);
        $content = NotificationService::getPaidPaymentContent();
        $note = NotificationService::getCounselingReminderNote();
        $footer = NotificationService::getGeneralFooter();

        return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'), 'Consigment')
            ->subject('Paid Payment Notification #'.$this->invoice->number.' & Counseling Information')
            ->greeting('Hi '.$notifiable->name)
            ->line(new HtmlString($header))
            ->line('Below are the details of your counseling session:')
            ->line(new HtmlString($table))
            ->line(new HtmlString($content))
            ->line(new HtmlString($note))
            ->line(new HtmlString($footer))
            ->view('mail.base');

        /* return (new MailMessage)
            ->from(env('MAIL_FROM_ADDRESS'), 'Personal Growth')
            ->subject('Paid Payment Notification #'.$this->invoice->number.' & Counseling Information')
            ->view('mail.invoice.success-payment', ['invoice' => $this->invoice]); */
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
            'subject' => 'Paid Payment Notification #'.$this->invoice->number.' & Counseling Information',
            'message' => 'Thank you for your payment! Please check your account page to see your booking details and fill in Counseling Form.'
        ];
    }
}
