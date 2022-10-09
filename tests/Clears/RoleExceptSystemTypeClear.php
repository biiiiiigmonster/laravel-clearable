<?php

namespace BiiiiiigMonster\Clearable\Tests\Clears;

use BiiiiiigMonster\Clearable\Contracts\ClearsAttributes;
use BiiiiiigMonster\Clearable\Tests\Models\Role;

class RoleExceptSystemTypeClear implements ClearsAttributes
{
    /**
     * @param Role $clear
     * @return bool
     */
    public function abandon($clear): bool
    {
        return !($clear->name && $clear->pivot->type % 2);
    }
}
