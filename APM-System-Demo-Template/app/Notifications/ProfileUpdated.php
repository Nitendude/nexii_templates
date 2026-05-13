<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class ProfileUpdated extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private string $adminName, private array $changes = [])
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
        $changeList = $this->changes ? implode(', ', $this->changes) : null;
        $message = 'Your profile details were updated by ' . $this->adminName . '.';

        if ($changeList) {
            $message = $message . ' Updated: ' . $changeList . '.';
        }

        return [
            'title' => 'Profile updated',
            'message' => $message,
            'url' => route('my-profile'),
            'timestamp' => Carbon::now()->toDateTimeString(),
        ];
    }
}
