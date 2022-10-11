<?php

namespace BiiiiiigMonster\Clearable\Tests\Clears;

use BiiiiiigMonster\Clearable\Contracts\InvokableClear;
use BiiiiiigMonster\Clearable\Tests\Models\Role;

class RoleExceptSystemTypeClear implements InvokableClear
{
    /**
     * @param Role $clear
     * @return bool
     */
    public function __invoke($clear): bool
    {
        return !($clear->name && $clear->pivot->type % 2);
    }
}
