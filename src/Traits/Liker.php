<?php

namespace Dealskoo\Like\Traits;

use Dealskoo\Like\Models\Like;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait Liker
{
    public function like(Model $model)
    {
        $attributes = [
            'likeable_type' => $model->getMorphClass(),
            'likeable_id' => $model->getKey(),
            'user_id' => $this->getKey(),
        ];
        return Like::query()->where($attributes)->firstOr(function () use ($attributes) {
            return Like::unguarded(function () use ($attributes) {
                if ($this->relationLoaded('likes')) {
                    $this->unsetRelation('likes');
                }
                return Like::query()->create($attributes);
            });
        });
    }

    public function unlike(Model $model)
    {
        $like = Like::query()
            ->where('likeable_id', $model->getKey())
            ->where('likeable_type', $model->getMorphClass())
            ->where('user_id', $this->getKey())
            ->first();
        if ($like) {
            if ($this->relationLoaded('likes')) {
                $this->unsetRelation('likes');
            }
            return $like->delete();
        }
        return true;
    }

    public function toggleLike(Model $model)
    {
        return $this->hasLiked($model) ? $this->unlike($model) : $this->like($model);
    }

    public function hasLiked(Model $model)
    {
        $likes = $this->relationLoaded('likes') ? $this->likes : $this->likes();
        return $likes->where('likeable_id', $model->getKey())->where('likeable_type', $model->getMorphClass())->count() > 0;
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id', $this->getKeyName());
    }

    public function getLikedItems(string $model)
    {
        return app($model)->whereHas('likers', function ($q) {
            return $q->where('user_id', $this->getKey());
        });
    }
}
