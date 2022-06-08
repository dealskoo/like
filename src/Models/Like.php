<?php

namespace Dealskoo\Like\Models;

use Dealskoo\Like\Events\Liked;
use Dealskoo\Like\Events\Unliked;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{

    protected $dispatchesEvents = [
        'created' => Liked::class,
        'deleted' => Unliked::class
    ];

    public function likeable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'user_id');
    }

    public function liker()
    {
        return $this->user();
    }

    public function scopeWithType(Builder $builder, string $type)
    {
        return $builder->where('likeable_type', app($type)->getMorphClass());
    }
}
