<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class CashAdvanceSubmitted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private string $employeeName, private string $amount)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Cash Advance request submitted',
            'message' => "{$this->employeeName} requested a cash advance of PHP " . number_format($this->amount, 2) . '.',
            'url' => route('accounting.cash-advances.index'),
            'timestamp' => Carbon::now()->toDateTimeString(),
        ];
    }
}
