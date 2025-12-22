<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReviewPosted extends Notification
{
    use Queueable;

    public function __construct(
        public array $payload
    ) {
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'review_posted',
            'title' => $this->payload['title'] ?? 'New review posted',
            'message' => $this->payload['message'] ?? '',
            'url' => $this->payload['url'] ?? null,
            'review_id' => $this->payload['review_id'] ?? null,
            'restaurant_id' => $this->payload['restaurant_id'] ?? null,
        ];
    }
}
