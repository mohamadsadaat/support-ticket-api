<?php

namespace App\Notifications;

use App\Models\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReplyAddedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reply $reply)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Reply on Ticket #'.$this->reply->ticket_id)
            ->line('A new reply was added:')
            ->line($this->reply->body)
            ->action('View Ticket', url("/tickets/{$this->reply->ticket_id}"));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->reply->ticket_id,
            'reply_id' => $this->reply->id,
            'body' => $this->reply->body,
        ];
    }
}
