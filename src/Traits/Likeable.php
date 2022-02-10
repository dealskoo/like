<?php

namespace Dealskoo\Like\Traits;

use Illuminate\Database\Eloquent\Model;

trait Likeable
{
    public function isLikedBy(Model $user)
    {
        if (is_a($user, config('auth.providers.users.model'))) {
            if ($this->relationLoaded('likers')) {
                return $this->likers->contains($user);
            }
            return $this->likers()->where('user_id', $user->getKey())->exists();
        }
        return false;
    }

    public function likers()
    {
        return $this->belongsToMany(config('auth.providers.users.model'), 'likes', 'likeable_id', 'user_id')->where('likeable_type', $this->getMorphClass());
    }
}
