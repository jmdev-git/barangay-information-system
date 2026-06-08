<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to a resident when an admin rejects their account (Req 2 AC3).
 * Must include the admin-provided rejection reason.
 */
class AccountRejectedNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $reason
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Barangay Account Registration Was Not Approved')
            ->greeting("Hello, {$notifiable->name},")
            ->line('After reviewing your submitted documents, your barangay account registration was not approved.')
            ->line('**Reason for rejection:**')
            ->line($this->reason)
            ->line('If you believe this is an error or would like to resubmit with corrected documents, please visit the barangay hall or contact us.')
            ->line('**Account Status:** Rejected')
            ->salutation('Barangay Management Information System');
    }
}
