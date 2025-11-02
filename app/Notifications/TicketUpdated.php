<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Ticket;

class TicketUpdated extends Notification
{
    use Queueable;

    public $ticket;
    public $updateType;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, $updateType = 'general')
    {
        $this->ticket = $ticket;
        $this->updateType = $updateType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $updateMessage = 'Ticket has been updated';
        
        if ($this->updateType === 'status') {
            $updateMessage = 'Status has been updated to: ' . ($this->ticket->status->name ?? 'Unknown');
        } elseif ($this->updateType === 'category') {
            $updateMessage = 'Category has been updated to: ' . ($this->ticket->category->name ?? 'Unknown');
        } elseif ($this->updateType === 'comment') {
            $updateMessage = 'A new comment has been added';
        }

        return (new MailMessage)
            ->subject('Ticket Updated: ' . $this->ticket->title)
            ->line('A ticket has been updated.')
            ->line('Title: ' . $this->ticket->title)
            ->line('Update: ' . $updateMessage)
            ->action('View Ticket', route('tickets.show', $this->ticket))
            ->line('Please review the changes.');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $updateMessage = 'Ticket has been updated';
        
        if ($this->updateType === 'status') {
            $updateMessage = 'Status updated to: ' . ($this->ticket->status->name ?? 'Unknown');
        } elseif ($this->updateType === 'category') {
            $updateMessage = 'Category updated to: ' . ($this->ticket->category->name ?? 'Unknown');
        } elseif ($this->updateType === 'comment') {
            $updateMessage = 'New comment added';
        }

        return [
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'update_type' => $this->updateType,
            'message' => $updateMessage,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
