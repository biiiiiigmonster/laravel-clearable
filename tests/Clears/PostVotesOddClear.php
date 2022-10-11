<?php

namespace BiiiiiigMonster\Clearable\Tests\Clears;

use BiiiiiigMonster\Clearable\Contracts\InvokableClear;

class PostVotesOddClear implements InvokableClear
{
    public function __invoke($clear): bool
    {
        return $clear->votes % 2;
    }
}
