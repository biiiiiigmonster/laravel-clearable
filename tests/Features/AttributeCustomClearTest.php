<?php

use BiiiiiigMonster\Clearable\Tests\Models\Post;
use BiiiiiigMonster\Clearable\Tests\Models\RoleUser;
use BiiiiiigMonster\Clearable\Tests\Models\UserAttributeCustom;

test('HasOneOrMany attribute custom clear test', function () {
    $user = UserAttributeCustom::has('posts', '>=', 2)->with('posts')->first();
    $posts = $user->posts;
    $postOdds = $posts->filter(fn ($post) => $post->votes % 2);
    $user->delete();

    expect(Post::whereKey($posts->pluck('id')->all())->count())->toEqual($posts->count() - $postOdds->count());
});

test('BelongsToMany attribute custom clear test', function () {
    $user = UserAttributeCustom::inRandomOrder()->first();
    $userId = $user->id;
    $roles = $user->roles;
    $roleClears = $roles->filter(fn ($role) => !($role->name && $role->pivot->type % 2));
    $user->delete();

    expect(RoleUser::where($user->roles()->getForeignPivotKeyName(), $userId)->count())->toEqual($roles->count() - $roleClears->count());
});
