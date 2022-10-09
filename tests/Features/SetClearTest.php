<?php

use BiiiiiigMonster\Clearable\Exceptions\NotAllowedClearsException;
use BiiiiiigMonster\Clearable\Tests\Models\Post;
use BiiiiiigMonster\Clearable\Tests\Models\RoleUser;
use BiiiiiigMonster\Clearable\Tests\Models\User;

test('HasOneOrMany set clear test', function () {
    $user = User::has('posts', '>=', 2)->with('posts')->first();
    $postIds = $user->posts->pluck('id')->all();
    $user->clear('posts')->delete();

    expect(Post::whereKey($postIds)->count())->toEqual(0);
});

test('BelongsToMany set clear test', function () {
    $user = User::inRandomOrder()->first();
    $userId = $user->id;
    $user->clear('roles')->delete();

    expect(RoleUser::where($user->roles()->getForeignPivotKeyName(), $userId)->count())->toEqual(0);
});

test('BelongsTo set clear test', function () {
    $user = User::inRandomOrder()->first();
    $relation = 'supplier';

    expect(fn () => $user->clear($relation)->delete())->toThrow(NotAllowedClearsException::class, sprintf(
        '%s::%s is relationship of %s, it not allowed to be cleared.',
        $user::class,
        $relation,
        $user->{$relation}()::class
    ));
});

test('Normal set clear test', function () {
    $user = User::inRandomOrder()->first();
    $normal = 'getEmail';

    expect(fn () => $user->clear($normal)->delete())->toThrow(LogicException::class, sprintf(
        '%s::%s must return a relationship instance.',
        $user::class,
        $normal
    ));
});
