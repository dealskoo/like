<?php

namespace Dealskoo\Like\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Unliked extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}
