<?php

namespace BiiiiiigMonster\Clearable\Tests\Clears;

use BiiiiiigMonster\Clearable\Contracts\InvokableClear;

class NormalClear implements InvokableClear
{
    public function __invoke($clear): bool
    {
        return true;
    }
}
