<?php
namespace BiiiiiigMonster\Cleans\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CleansAttributes
{
    /**
     * Decide if the cleanable retained.
     *
     * @param Model $clean
     * @param Model $model
     * @return bool
     */
    public function retain(Model $clean, Model $model): bool;
}