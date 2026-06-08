<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to a resident right after they complete registration (Req 1 AC6).
 * Confirms that the account is pending admin review.
 */
class AccountPendingNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Barangay Account is Pending Verification')
            ->greeting("Hello, {$notifiable->name}!")
            ->line('Thank you for registering with the Barangay Management Information System.')
            ->line('Your account has been submitted for review. A barangay admin will review your uploaded government ID and notify you once your account has been approved.')
            ->line('**Account Status:** Pending Verification')
            ->line('You will receive another email when your account has been reviewed.')
            ->salutation('Barangay Management Information System');
    }
}
