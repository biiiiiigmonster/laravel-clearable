<?php

use BiiiiiigMonster\Clearable\Tests\Models\Post;
use BiiiiiigMonster\Clearable\Tests\Models\RoleUser;
use BiiiiiigMonster\Clearable\Tests\Models\UserAttribute;

test('HasOneOrMany attribute clear test', function () {
    $user = UserAttribute::has('posts', '>=', 2)->with('posts')->first();
    $postIds = $user->posts->pluck('id')->all();
    $user->delete();

    expect(Post::whereKey($postIds)->count())->toEqual(0);
});

test('BelongsToMany attribute clear test', function () {
    $user = UserAttribute::inRandomOrder()->first();
    $userId = $user->id;
    $user->delete();

    expect(RoleUser::where($user->roles()->getForeignPivotKeyName(), $userId)->count())->toEqual(0);
});
