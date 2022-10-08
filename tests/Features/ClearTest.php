<?php

use BiiiiiigMonster\Clearable\Tests\Models\Post;
use BiiiiiigMonster\Clearable\Tests\Models\User;
use BiiiiiigMonster\Clearable\Tests\Models\UserAttribute;
use BiiiiiigMonster\Clearable\Tests\Models\UserProperty;

test('set clear test', function () {
    $user = User::has('posts', '>=', 2)->with('posts')->first();
    $postIds = $user->posts->pluck('id')->all();
    $user->clear('posts')->delete();

    expect(Post::whereKey($postIds)->count())->toEqual(0);
});

test('property clear test', function () {
    $user = UserProperty::has('posts', '>=', 2)->with('posts')->first();
    $postIds = $user->posts->pluck('id')->all();
    $user->delete();

    expect(Post::whereKey($postIds)->count())->toEqual(0);
});

test('attribute clear test', function () {
    $user = UserAttribute::has('posts', '>=', 2)->with('posts')->first();
    $postIds = $user->posts->pluck('id')->all();
    $user->delete();

    expect(Post::whereKey($postIds)->count())->toEqual(0);
});
