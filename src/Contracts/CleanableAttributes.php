<?php
namespace BiiiiiigMonster\Cleanable\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CleanableAttributes
{
    /**
     * Decide if the cleanable retained.
     *
     * @param Model $cleanable
     * @param Model $model
     * @return bool
     */
    public function retain(Model $cleanable, Model $model): bool;
}