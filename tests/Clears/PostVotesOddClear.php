<?php

namespace BiiiiiigMonster\Clearable\Tests\Clears;

use BiiiiiigMonster\Clearable\Contracts\ClearsAttributes;

class PostVotesOddClear implements ClearsAttributes
{
    public function abandon($clear): bool
    {
        return $clear->votes % 2;
    }
}
