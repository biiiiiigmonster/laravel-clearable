<?php

namespace BiiiiiigMonster\Clearable\Tests\Clears;

use BiiiiiigMonster\Clearable\Contracts\InvokableClear;

class SupplierClear implements InvokableClear
{
    public function __invoke($clear): bool
    {
        return true;
    }
}
