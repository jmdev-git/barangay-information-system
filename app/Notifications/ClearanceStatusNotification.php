<?php

namespace App\Notifications;

use App\Models\Clearance;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to a resident when their clearance is rejected (Req 5 AC5).
 * Approval notification is handled by making the PDF available for download — no email needed per spec.
 */
class ClearanceStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Clearance $clearance,
        private readonly string    $action, // 'approved' | 'rejected'
        private readonly ?string   $reason = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        if ($this->action === 'approved') {
            return (new MailMessage)
                ->subject('Your Barangay Clearance Has Been Approved')
                ->greeting("Hello, {$notifiable->name}!")
                ->line('Your barangay clearance request has been approved.')
                ->line('You can now download your clearance certificate by logging in to the system.')
                ->action('Download Clearance', url('/clearances'))
                ->salutation('Barangay Management Information System');
        }

        return (new MailMessage)
            ->subject('Your Barangay Clearance Request Was Not Approved')
            ->greeting("Hello, {$notifiable->name},")
            ->line('Your barangay clearance request could not be approved at this time.')
            ->line('**Reason:**')
            ->line($this->reason ?? 'No specific reason provided.')
            ->line('You may submit a new request after resolving the issue mentioned above.')
            ->salutation('Barangay Management Information System');
    }
}
