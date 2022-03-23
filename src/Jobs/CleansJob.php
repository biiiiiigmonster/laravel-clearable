<?php

namespace BiiiiiigMonster\Cleans\Jobs;

use BiiiiiigMonster\Cleans\Cleaner;
use BiiiiiigMonster\Cleans\Contracts\CleansAttributes;
use BiiiiiigMonster\Cleans\Exceptions\NotAllowedCleansException;
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

class CleansJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels, InteractsWithQueue;

    /**
     * CleanJob constructor.
     *
     * @param Model $model
     * @param string $relationName
     * @param CleansAttributes|null $cleansAttributes
     * @param bool $cleanWithSoftDelete
     * @param bool $isForce
     */
    public function __construct(
        protected Model $model,
        protected string $relationName,
        protected ?CleansAttributes $cleansAttributes = null,
        protected bool $cleanWithSoftDelete = false,
        protected bool $isForce = false
    )
    {
    }

    /**
     * CleanJob handle.
     *
     * @throws NotAllowedCleansException
     */
    public function handle(): void
    {
        /** @var Relation $relation */
        $relation = $this->model->{$this->relationName}();

        // to be cleaned model.
        $cleans = $relation->lazy()->filter(fn(Model $clean) =>
            $this->cleansAttributes?->confirm($clean, $this->model)
                ?? $this->isForce || $this->cleanWithSoftDelete || !Cleaner::hasSoftDeletes($this->model)
        );

        match ($relation::class) {
            HasOne::class, HasOneThrough::class, MorphOne::class,
            HasMany::class, HasManyThrough::class, MorphMany::class => $cleans->each(function (Model $clean){
                $this->isForce ? $clean->forceDelete() : $clean->delete();
            }),
            BelongsToMany::class, MorphToMany::class => $relation->detach(
                $cleans->pluck($relation->getRelatedKeyName())->all()
            ),
            BelongsTo::class, MorphTo::class => throw new NotAllowedCleansException(sprintf(
                '%s::%s is relationship of %s, it not allowed to be cleaned.',
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