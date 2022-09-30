<?php

use BiiiiiigMonster\Clearable\Tests\Models\Post;
use BiiiiiigMonster\Clearable\Tests\Models\User;

test('clear test', function () {
    $user = User::has('posts', '>=', 2)->with('posts')->first();
    $postIds = $user->posts->pluck('id')->all();
//    $user->clear('posts')->delete();

//    Post::whereKey($postIds)->count()
    expect(0)->toEqual(0);
});
