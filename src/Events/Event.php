<?php

namespace Dealskoo\Like\Events;

use Illuminate\Database\Eloquent\Model;

class Event
{
    public $like;

    public function __construct(Model $like)
    {
        $this->like = $like;
    }
}
