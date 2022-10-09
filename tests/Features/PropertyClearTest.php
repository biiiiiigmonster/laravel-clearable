<?php

use BiiiiiigMonster\Clearable\Tests\Models\Post;
use BiiiiiigMonster\Clearable\Tests\Models\RoleUser;
use BiiiiiigMonster\Clearable\Tests\Models\UserProperty;

test('HasOneOrMany property clear test', function () {
    $user = UserProperty::has('posts', '>=', 2)->with('posts')->first();
    $postIds = $user->posts->pluck('id')->all();
    $user->delete();

    expect(Post::whereKey($postIds)->count())->toEqual(0);
});

test('BelongsToMany property clear test', function () {
    $user = UserProperty::inRandomOrder()->first();
    $userId = $user->id;
    $user->delete();

    expect(RoleUser::where($user->roles()->getForeignPivotKeyName(), $userId)->count())->toEqual(0);
});
