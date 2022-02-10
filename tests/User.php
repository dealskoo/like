<?php

namespace Dealskoo\Like\Tests;

use Dealskoo\Like\Traits\Liker;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Liker;

    protected $fillable = ['name'];
}
