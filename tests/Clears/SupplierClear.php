<?php

namespace BiiiiiigMonster\Clearable\Tests\Clears;

use BiiiiiigMonster\Clearable\Contracts\ClearsAttributes;

class SupplierClear implements ClearsAttributes
{
    public function abandon($clear): bool
    {
        return true;
    }
}
