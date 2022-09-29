<?php

namespace BiiiiiigMonster\Clears\Jobs;

use BiiiiiigMonster\Clears\Exceptions\NotAllowedClearsException;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LogicException;
use ReflectionMethod;

class ClearsJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * ClearJob constructor.
     *
     * @param Model $model
     * @param string $relationName
     * @param string|null $clearsAttributesClassName
     */
    public function __construct(
        protected Model $model,
        protected string $relationName,
        protected ?string $clearsAttributesClassName = null,
    ) {
    }

    /**
     * ClearJob handle.
     *
     * @throws NotAllowedClearsException
     */
    public function handle(): void
    {
        /** @var Relation $relation */
        $relation = $this->model->{$this->relationName}();

        // to be cleared model.
        $clears = $relation->lazy();
        try {
            $rfc = new ReflectionMethod($this->clearsAttributesClassName, 'reserve');
            $clears = $clears->reject(fn (Model $clear) => $rfc->invokeArgs(null, [$clear, $this->model]));
        } catch (Exception) {
        }

        match ($relation::class) {
            HasOne::class, HasOneThrough::class, MorphOne::class,
            HasMany::class, HasManyThrough::class, MorphMany::class => $clears->map(
                fn (Model $clear) => $clear->delete()
            ),
            BelongsToMany::class, MorphToMany::class => $relation->detach(
                $clears->pluck($relation->getRelatedKeyName())->all()
            ),
            BelongsTo::class, MorphTo::class => throw new NotAllowedClearsException(sprintf(
                '%s::%s is relationship of %s, it not allowed to be cleared.',
                $this->model::class,
                $this->relationName,
                $relation::class
            )),
            default => throw new LogicException(sprintf(
                '%s::%s must return a relationship instance.',
                $this->model::class,
                $this->relationName
            ))
        };
    }
}
