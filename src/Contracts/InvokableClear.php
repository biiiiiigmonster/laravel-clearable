<?php

namespace BiiiiiigMonster\Clearable\Contracts;

use Illuminate\Database\Eloquent\Model;

interface InvokableClear
{
    /**
     * Decide if the clearable cleared.
     *
     * @param Model $clear
     * @return bool
     */
    public function __invoke($clear);
}
