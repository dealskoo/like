<?php

namespace Dealskoo\Like\Events;

use Dealskoo\Like\Models\Like;

class Event
{
    public $like;

    public function __construct(Like $like)
    {
        $this->like = $like;
    }
}
