<?php

namespace Dealskoo\Like\Tests\Feature;

use Closure;
use Dealskoo\Like\Events\Liked;
use Dealskoo\Like\Events\Unliked;
use Dealskoo\Like\Tests\Post;
use Dealskoo\Like\Tests\Product;
use Dealskoo\Like\Tests\TestCase;
use Dealskoo\Like\Tests\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_basic_features()
    {
        Event::fake();
        $user = User::create(['name' => 'user']);
        $post = Post::create(['title' => 'test guide']);
        $user->like($post);
        Event::assertDispatched(Liked::class, function ($event) use ($user, $post) {
            $like = $event->like;
            return $like->likeable instanceof Post && $like->user instanceof User && $like->user->id == $user->id && $like->likeable->id == $post->id;
        });
        $this->assertTrue($user->hasLiked($post));
        $this->assertTrue($post->isLikedBy($user));
        $this->assertTrue($user->unlike($post));

        Event::assertDispatched(Unliked::class, function ($event) use ($user, $post) {
            return $event->like->likeable instanceof Post && $event->like->user instanceof User && $event->like->user->id == $user->id && $event->like->likeable->id == $post->id;
        });
    }

    public function test_unlike_features()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2']);
        $user3 = User::create(['name' => 'user3']);
        $post = Post::create(['title' => 'test post']);

        $user1->like($post);
        $user1->like($post);
        $user2->like($post);
        $user3->like($post);

        $user2->unlike($post);

        $this->assertFalse($user2->hasLiked($post));
        $this->assertTrue($user1->hasLiked($post));
        $this->assertTrue($user3->hasLiked($post));
        $this->assertCount(1, $user1->likes);
    }

    public function test_aggregations()
    {
        $user = User::create(['name' => 'user']);

        $post1 = Post::create(['title' => 'post1']);
        $post2 = Post::create(['title' => 'post2']);

        $product1 = Product::create(['name' => 'product1']);
        $product2 = Product::create(['name' => 'product2']);

        $user->like($post1);
        $user->like($post2);
        $user->like($product1);
        $user->like($product2);

        $this->assertCount(4, $user->likes);
        $this->assertCount(2, $user->likes()->withType(Post::class)->get());
    }

    public function test_object_liker()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2']);
        $user3 = User::create(['name' => 'user3']);

        $post = Post::create(['title' => 'test post']);

        $user1->like($post);
        $user2->like($post);
        $this->assertCount(2, $post->likers);
        $this->assertCount(2, $post->likes);

        $this->assertSame($user1->name, $post->likers[0]['name']);
        $this->assertSame($user2->name, $post->likers[1]['name']);

        $sqls = $this->getQueryLog(function () use ($post, $user1, $user2, $user3) {
            $this->assertTrue($post->isLikedBy($user1));
            $this->assertTrue($post->isLikedBy($user2));
            $this->assertFalse($post->isLikedBy($user3));
        });

        $this->assertEmpty($sqls->all());
    }

    public function test_eager_loading()
    {
        $user = User::create(['name' => 'user']);

        $post1 = Post::create(['title' => 'post1']);
        $post2 = Post::create(['title' => 'post2']);

        $product1 = Product::create(['name' => 'product1']);
        $product2 = Product::create(['name' => 'product2']);

        $user->like($post1);
        $user->like($post2);
        $user->like($product1);
        $user->like($product2);

        $sqls = $this->getQueryLog(function () use ($user) {
            $user->load('likes.likeable');
        });

        $this->assertCount(3, $sqls);

        $sqls = $this->getQueryLog(function () use ($user, $post1) {
            $user->hasLiked($post1);
        });

        $this->assertEmpty($sqls->all());
    }

    protected function getQueryLog(Closure $callback): Collection
    {
        $sqls = collect([]);
        DB::listen(function ($query) use ($sqls) {
            $sqls->push(['sql' => $query->sql, 'bindings' => $query->bindings]);
        });
        $callback();
        return $sqls;
    }
}
