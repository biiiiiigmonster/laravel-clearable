<?php
namespace BiiiiiigMonster\Cleans\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CleansAttributes
{
    /**
     * Decide if the cleanable cleaned.
     *
     * @param Model $clean
     * @param Model $model
     * @return bool
     */
    public function confirm(Model $clean, Model $model): bool;
}