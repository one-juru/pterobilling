<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoicePaidNotif extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The invoice instance.
     *
     * @var \App\Models\Invoice
     */
    protected $invoice;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice)
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->subject('Your invoice (#'.$this->invoice->id.') has been paid successfully')->view('emails.notif', [
            'subject' => 'Invoice Paid',
            'body_message' => 'Thank you for ordering a product or service from us. You have successfully paid for the invoice (#'.$this->invoice->id.').',
            'body_action' => 'You may click the button below to view the invoice details.',
            'button_text' => 'View Invoice',
            'button_url' => url()->route('client.invoice.show', ['id' => $this->invoice->id]),
            'notice' => 'You received this email because you have completed your payment for your order.',
        ]);
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
