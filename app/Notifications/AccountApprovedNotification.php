<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to a resident when an admin approves their account (Req 2 AC2).
 */
class AccountApprovedNotification extends Notification
{
    use Queueable;

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Barangay Account Has Been Approved')
            ->greeting("Hello, {$notifiable->name}!")
            ->line('Great news! Your barangay account has been reviewed and approved.')
            ->line('You can now log in to access the Barangay Management Information System and request barangay clearances, file blotter reports, and more.')
            ->action('Log In Now', url('/login'))
            ->line('**Account Status:** Active')
            ->salutation('Barangay Management Information System');
    }
}
