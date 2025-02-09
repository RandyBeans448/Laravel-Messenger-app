<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $conversationUuid;

    /**
     * Create a new event instance.
     */
    public function __construct($message, $conversationUuid)
    {
        $this->message = $message;
        $this->conversationUuid = $conversationUuid;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->conversationUuid),
        ];
    }

    /**
     * Name of the event for Echo
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }
}
