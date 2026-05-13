<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class ProfileCorrectionRequested extends Notification
{
    use Queueable;

    public function __construct(private string $employeeName, private string $section)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Profile correction request',
            'message' => "{$this->employeeName} requested a correction for {$this->section}.",
            'url' => route('admin.profile-corrections.index'),
            'timestamp' => Carbon::now()->toDateTimeString(),
        ];
    }
}
