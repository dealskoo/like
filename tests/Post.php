<?php

namespace Dealskoo\Like\Tests;

use Dealskoo\Like\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Likeable;

    protected $fillable = ['title'];
}
