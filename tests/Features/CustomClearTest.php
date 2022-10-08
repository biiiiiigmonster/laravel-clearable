<?php

use BiiiiiigMonster\Clearable\Tests\Clears\PostVotesOddClear;
use BiiiiiigMonster\Clearable\Tests\Models\Post;
use BiiiiiigMonster\Clearable\Tests\Models\User;
use BiiiiiigMonster\Clearable\Tests\Models\UserAttributeCustom;
use BiiiiiigMonster\Clearable\Tests\Models\UserPropertyCustom;

test('set custom clear test', function () {
    $user = User::has('posts', '>=', 2)->with('posts')->first();
    $posts = $user->posts;
    $postOdds = $posts->filter(fn ($post) => $post->votes % 2);
    $user->setClears(['posts' => PostVotesOddClear::class])->delete();

    expect(Post::whereKey($posts->pluck('id')->all())->count())->toEqual($posts->count() - $postOdds->count());
});

test('property custom clear test', function () {
    $user = UserPropertyCustom::has('posts', '>=', 2)->with('posts')->first();
    $posts = $user->posts;
    $postOdds = $posts->filter(fn ($post) => $post->votes % 2);
    $user->delete();

    expect(Post::whereKey($posts->pluck('id')->all())->count())->toEqual($posts->count() - $postOdds->count());
});

test('attribute custom clear test', function () {
    $user = UserAttributeCustom::has('posts', '>=', 2)->with('posts')->first();
    $posts = $user->posts;
    $postOdds = $posts->filter(fn ($post) => $post->votes % 2);
    $user->delete();

    expect(Post::whereKey($posts->pluck('id')->all())->count())->toEqual($posts->count() - $postOdds->count());
});