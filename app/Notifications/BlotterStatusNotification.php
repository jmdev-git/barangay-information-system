<?php

namespace App\Notifications;

use App\Models\Blotter;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to a resident when their submitted blotter is approved or rejected (Req 6 AC3, AC4).
 */
class BlotterStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Blotter $blotter,
        private readonly string  $action, // 'approved' | 'rejected'
        private readonly ?string $reason = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $caseNumber = $this->blotter->case_number;

        if ($this->action === 'approved') {
            return (new MailMessage)
                ->subject("Blotter {$caseNumber} — Approved")
                ->greeting("Hello, {$notifiable->name}!")
                ->line("Your blotter report ({$caseNumber}) has been reviewed and approved.")
                ->line('The case is now officially open. The barangay will be in contact regarding next steps.')
                ->salutation('Barangay Management Information System');
        }

        return (new MailMessage)
            ->subject("Blotter {$caseNumber} — Not Approved")
            ->greeting("Hello, {$notifiable->name},")
            ->line("Your blotter report ({$caseNumber}) was reviewed and could not be approved.")
            ->line('**Reason:**')
            ->line($this->reason ?? 'No specific reason provided.')
            ->line('Please visit the barangay hall if you wish to discuss this further.')
            ->salutation('Barangay Management Information System');
    }
}
