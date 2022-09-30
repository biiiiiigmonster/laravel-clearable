<?php

namespace BiiiiiigMonster\Clearable\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ClearsAttributes
{
    /**
     * Decide if the clearable cleared.
     *
     * @param Model $clear
     * @param Model $model
     * @return bool
     */
    public function reserve(Model $clear, Model $model): bool;
}
