<?php

namespace BiiiiiigMonster\Clearable\Jobs;

use BiiiiiigMonster\Clearable\Contracts\InvokableClear;
use BiiiiiigMonster\Clearable\Exceptions\NotAllowedClearsException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;
use LogicException;

class ClearsJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    /**
     * ClearJob constructor.
     *
     * @param Model $model
     * @param string $relationName
     * @param string|null $invokableClearClassName
     */
    public function __construct(
        protected Model $model,
        protected string $relationName,
        protected ?string $invokableClearClassName,
    ) {
    }

    /**
     * ClearJob handle.
     *
     * @throws NotAllowedClearsException
     */
    public function handle(): void
    {
        $relation = $this->model->{$this->relationName}();

        // to be cleared model.
        $clears = Collection::wrap($this->model->{$this->relationName});
        if (is_a($this->invokableClearClassName, InvokableClear::class, true)) {
            $invoke = new $this->invokableClearClassName();
            $clears = $clears->filter(fn (Model $clear) => $invoke($clear));
        }

        switch (true) {
            case $relation instanceof HasOneOrMany:
                $clears->map(fn (Model $clear) => $clear->delete());
                break;
            case $relation instanceof BelongsToMany:
                $relation->detach($clears->pluck($relation->getRelatedKeyName())->all());
                break;
            case $relation instanceof BelongsTo:
                throw new NotAllowedClearsException(sprintf(
                    '%s::%s is relationship of %s, it not allowed to be cleared.',
                    $this->model::class,
                    $this->relationName,
                    $relation::class
                ));
            default:
                throw new LogicException(sprintf(
                    '%s::%s must return a relationship instance.',
                    $this->model::class,
                    $this->relationName
                ));
        }
    }
}
