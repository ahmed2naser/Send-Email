<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class emailCode extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('The Massage Code')
            ->line('Your Code Is' . $notifiable->code)
            ->action('Enter', route('verify.index'))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
