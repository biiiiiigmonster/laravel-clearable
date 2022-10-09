<?php

use BiiiiiigMonster\Clearable\Exceptions\NotAllowedClearsException;
use BiiiiiigMonster\Clearable\Tests\Clears\NormalClear;
use BiiiiiigMonster\Clearable\Tests\Clears\PostVotesOddClear;
use BiiiiiigMonster\Clearable\Tests\Clears\RoleExceptSystemTypeClear;
use BiiiiiigMonster\Clearable\Tests\Clears\SupplierClear;
use BiiiiiigMonster\Clearable\Tests\Models\Post;
use BiiiiiigMonster\Clearable\Tests\Models\RoleUser;
use BiiiiiigMonster\Clearable\Tests\Models\User;

test('HasOneOrMany set custom clear test', function () {
    $user = User::has('posts', '>=', 2)->with('posts')->first();
    $posts = $user->posts;
    $postOdds = $posts->filter(fn ($post) => $post->votes % 2);
    $user->setClears(['posts' => PostVotesOddClear::class])->delete();

    expect(Post::whereKey($posts->pluck('id')->all())->count())->toEqual($posts->count() - $postOdds->count());
});

test('BelongsToMany set custom clear test', function () {
    $user = User::inRandomOrder()->first();
    $userId = $user->id;
    $roles = $user->roles;
    $roleClears = $roles->filter(fn ($role) => !($role->name && $role->pivot->type % 2));
    $user->setClears(['roles' => RoleExceptSystemTypeClear::class])->delete();

    expect(RoleUser::where($user->roles()->getForeignPivotKeyName(), $userId)->count())->toEqual($roles->count() - $roleClears->count());
});

test('BelongsTo set clear test', function () {
    $user = User::inRandomOrder()->first();
    $relation = 'supplier';

    expect(fn () => $user->setClears([$relation => SupplierClear::class])->delete())->toThrow(NotAllowedClearsException::class, sprintf(
        '%s::%s is relationship of %s, it not allowed to be cleared.',
        $user::class,
        $relation,
        $user->{$relation}()::class
    ));
});

test('Normal set clear test', function () {
    $user = User::inRandomOrder()->first();
    $normal = 'getEmail';

    expect(fn () => $user->setClears([$normal => NormalClear::class])->delete())->toThrow(LogicException::class, sprintf(
        '%s::%s must return a relationship instance.',
        $user::class,
        $normal
    ));
});
