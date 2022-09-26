<?php

namespace BiiiiiigMonster\Clears\Contracts;

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
    public function confirm(Model $clear, Model $model): bool;
}
