<?php
namespace Biiiiiigmonster\Cleanable\Contracts;

use Illuminate\Database\Eloquent\Model;

interface CleanableAttributes
{
    /**
     * Decide if the cleanable model will be deleted.
     *
     * @param Model $model
     * @param Model $cleanable
     * @return bool
     */
    public function decide(Model $model, Model $cleanable): bool;
}