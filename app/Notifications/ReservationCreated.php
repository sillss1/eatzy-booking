<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationCreated extends Notification
{
    use Queueable;

    public function __construct(
        public array $payload
    ) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'reservation_created',
            'title' => $this->payload['title'] ?? 'New reservation',
            'message' => $this->payload['message'] ?? '',
            'url' => $this->payload['url'] ?? null,
            'reservation_id' => $this->payload['reservation_id'] ?? null,
            'restaurant_id' => $this->payload['restaurant_id'] ?? null,
        ];
    }
}
