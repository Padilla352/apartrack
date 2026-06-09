<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $conversation;
    public $readerId;
    public $readerType;

    public function __construct(Conversation $conversation, $readerId, $readerType)
    {
        $this->conversation = $conversation;
        $this->readerId = $readerId;
        $this->readerType = $readerType;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('conversation.' . $this->conversation->id);
    }

    public function broadcastWith()
    {
        return [
            'reader_id' => $this->readerId,
            'reader_type' => $this->readerType,
        ];
    }
}