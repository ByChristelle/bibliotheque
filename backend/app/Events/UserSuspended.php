<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserSuspended implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public string $msg;

    /**
     * Create a new event instance.
     */
   public function __construct(int $userId , string $msg)
{
    $this->userId = $userId;

    $this->msg= $msg;
}

public function broadcastAs(): string
{
    return 'UserSuspended';
}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    // public function broadcastOn(): array
    // {
    //    return [
    // new PrivateChannel('user.' . $this->userId),
    //         ];
    // }

    public function broadcastWith() : array {
        return[
            'message'=> $this->msg,
            ] ;
    }


    public function broadcastOn(): array
    {
       return [
    new Channel('userChannel'),
            ];
    }
}
