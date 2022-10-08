<?php

namespace BiiiiiigMonster\Clearable\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ClearsAttributes
{
    /**
     * Decide if the clearable cleared.
     *
     * @param Model $clear
     * @return bool
     */
    public function abandon(Model $clear): bool;
}
