<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAnnouncementNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Announcement $announcement
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'new_announcement',
            'message' => 'New announcement: ' . $this->announcement->title,
            'url'     => url('/announcements'),
            'color'   => 'info',
            'icon'    => 'bi-megaphone-fill',
        ];
    }
}
