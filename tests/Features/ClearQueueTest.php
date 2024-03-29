<?php

use BiiiiiigMonster\Clearable\Jobs\ClearsJob;
use BiiiiiigMonster\Clearable\Tests\Models\User;
use Illuminate\Support\Facades\Queue;

test('Clear named queue test', function () {
    Queue::fake();

    $user = User::has('posts', '>=', 2)->with('posts')->first();
    $user->clear('posts')->setClearQueue('queue-name')->delete();

    Queue::assertPushedOn('queue-name', ClearsJob::class);
});
