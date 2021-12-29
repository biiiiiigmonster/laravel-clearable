<?php
namespace Biiiiiigmonster\Cleanable\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CleanableAttributes
{
    /**
     * Decide if the cleanable model will be deleted.
     *
     * @param Model $cleanable
     * @param Model $model
     * @return bool
     */
    public function decide(Model $cleanable, Model $model): bool;
}