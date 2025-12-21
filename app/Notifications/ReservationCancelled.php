<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReservationCancelled extends Notification
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
            'type' => 'reservation_cancelled',
            'title' => $this->payload['title'] ?? 'Reservation cancelled',
            'message' => $this->payload['message'] ?? '',
            'url' => $this->payload['url'] ?? null,
            'reservation_id' => $this->payload['reservation_id'] ?? null,
            'restaurant_id' => $this->payload['restaurant_id'] ?? null,
        ];
    }
}
