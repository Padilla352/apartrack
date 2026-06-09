<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TypingEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $userId;
    public $userType;
    public $isTyping;

    public function __construct(Conversation $conversation, $userId, $userType, $isTyping)
    {
        $this->conversation = $conversation;
        $this->userId = $userId;
        $this->userType = $userType;
        $this->isTyping = $isTyping;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('conversation.' . $this->conversation->id);
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->userId,
            'user_type' => $this->userType,
            'is_typing' => $this->isTyping,
        ];
    }
}