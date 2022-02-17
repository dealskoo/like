<?php

namespace Dealskoo\Like\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Liked extends Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}
