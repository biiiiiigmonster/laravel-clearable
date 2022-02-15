<?php

namespace Biiiiiigmonster\Cleanable\Jobs;

use Biiiiiigmonster\Cleanable\Cleanabler;
use Biiiiiigmonster\Cleanable\Contracts\CleanableAttributes;
use Biiiiiigmonster\Cleanable\Exceptions\NotAllowedCleanableException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
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

class CleanJob implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels, InteractsWithQueue;

    /**
     * CleanJob constructor.
     *
     * @param Model $model
     * @param string $relationName
     * @param CleanableAttributes|null $condition
     * @param bool $propagateSoftDelete
     * @param bool $isForce
     */
    public function __construct(
        protected Model $model,
        protected string $relationName,
        protected ?CleanableAttributes $condition = null,
        protected bool $propagateSoftDelete = true,
        protected bool $isForce = false
    )
    {
    }

    /**
     * CleanJob handle.
     *
     * @throws NotAllowedCleanableException
     */
    public function handle(): void
    {
        $cleanableModels = collect($this->model->getRelationValue($this->relationName))
            ->filter(
                static fn(Model $cleanable) => $this->condition instanceof CleanableAttributes
                    ? !$this->condition->retain($cleanable, $this->model)
                    : ($this->isForce || !$this->retainedDuringSoftDeletes())
            );

        $relation = $this->model->{$this->relationName}();
        match ($relation::class) {
            HasOne::class, HasOneThrough::class, MorphOne::class,
            HasMany::class, HasManyThrough::class, MorphMany::class => $cleanableModels->map(
                static fn(Model $relationModel) => $this->isForce
                    ? $relationModel->forceDelete()
                    : $relationModel->delete()
            ),
            BelongsToMany::class, MorphToMany::class => $relation->detach(
                $cleanableModels->pluck($relation->getRelatedKeyName())
            ),
            default => throw new NotAllowedCleanableException(sprintf(
                'The cleanable "%s::%s" is relationship of "%s", it not allowed to be cleaned.',
                $this->model,
                $this->relationName,
                $relation::class
            ))
        };
    }

    /**
     * Determine if the cleanable retained during normal deleting.
     *
     * @return bool
     */
    protected function retainedDuringSoftDeletes(): bool
    {
        // The static model must have "SoftDeletes" trait and close propagate soft delete.
        return Cleanabler::hasSoftDeletes($this->model) && !$this->propagateSoftDelete;
    }
}