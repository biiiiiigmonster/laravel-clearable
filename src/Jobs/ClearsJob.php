<?php

namespace BiiiiiigMonster\Clearable\Jobs;

use BiiiiiigMonster\Clearable\Exceptions\NotAllowedClearsException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use LogicException;
use ReflectionException;
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
     * @param string $className
     * @param string $relationName
     * @param EloquentCollection $collection
     * @param string|null $clearsAttributesClassName
     */
    public function __construct(
        protected string $className,
        protected string $relationName,
        protected EloquentCollection $collection,
        protected ?string $clearsAttributesClassName = null,
    ) {
    }

    /**
     * ClearJob handle.
     *
     * @throws NotAllowedClearsException
     * @throws ReflectionException
     */
    public function handle(): void
    {
        $rfc = new ReflectionMethod($this->className, $this->relationName);
        $relation = (string) $rfc->getReturnType();

        // to be cleared model.
        $clears = $this->clearsAttributesClassName
            ? $this->collection->filter(fn (Model $clear) => (new $this->clearsAttributesClassName())->abandon($clear))
            : $this->collection;

        match ($relation) {
            HasOne::class, HasOneThrough::class, MorphOne::class,
            HasMany::class, HasManyThrough::class, MorphMany::class => $clears->map(
                fn (Model $clear) => $clear->delete()
            ),
            BelongsToMany::class, MorphToMany::class => $relation->detach(
                $clears->pluck($relation->getRelatedKeyName())->all()
            ),
            BelongsTo::class, MorphTo::class => throw new NotAllowedClearsException(sprintf(
                '%s::%s is relationship of %s, it not allowed to be cleared.',
                $this->className,
                $this->relationName,
                $relation
            )),
            default => throw new LogicException(sprintf(
                '%s::%s must return a relationship instance.',
                $this->className,
                $this->relationName
            ))
        };
    }
}
