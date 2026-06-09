<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use stdClass;

class ApartmentAddedNotification extends Notification
{
    use Queueable;

    protected $apartment;

    /**
     * Create a new notification instance.
     */
    public function __construct($apartment)
    {
        $this->apartment = $apartment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // 'database' channel is for storing in the notifications table.
        // 'broadcast' channel is for real-time WebSocket events (optional).
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'id' => $this->apartment->id,
            'title' => 'New Apartment Available',
            'message' => "A new apartment '{$this->apartment->name}' has been added in {$this->apartment->barangay_name}.",
            'icon' => '🏢',
            'apartment_id' => $this->apartment->id,
            'created_at' => now()->toISOString(),
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->apartment->id,
            'title' => 'New Apartment Available',
            'message' => "A new apartment '{$this->apartment->name}' has been added in {$this->apartment->barangay_name}.",
            'icon' => '🏢',
            'created_at' => now()->toISOString(),
        ]);
    }
}