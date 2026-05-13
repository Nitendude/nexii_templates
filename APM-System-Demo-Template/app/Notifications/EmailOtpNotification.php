<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailOtpNotification extends Notification
{
    use Queueable;

    public function __construct(private string $code, private int $minutes)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your APM Email Verification Code')
            ->greeting('Hello!')
            ->line('Use the code below to verify your email for APM Customs Brokerage.')
            ->line("Verification code: {$this->code}")
            ->line("This code expires in {$this->minutes} minutes.")
            ->line('If you did not request this, you can ignore this email.')
            ->salutation('Regards, APM Customs Brokerage');
    }
}
