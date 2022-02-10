<?php

namespace Dealskoo\Like\Tests;

use Dealskoo\Like\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Likeable;

    protected $fillable = ['name'];
}
