<?php

namespace Biiiiiigmonster\Cleanable\Jobs;

use Biiiiiigmonster\Cleanable\Contracts\CleanableAttributes;
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
use Illuminate\Database\Eloquent\SoftDeletes;
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
     */
    public function handle(): void
    {
        $cleanableModels = collect($this->model->getRelationValue($this->relationName))
            ->filter(
                static fn(Model $relationModel) => $this->condition instanceof CleanableAttributes
                    ? $this->condition->decide($relationModel, $this->model)
                    : ($this->isForce || $this->propagateSoftDelete || !$this->isSoftDeleteModel())
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
            )
        };
    }

    /**
     * Determine if the model use 'SoftDeletes' trait.
     *
     * @return bool
     */
    protected function isSoftDeleteModel(): bool
    {
        return isset(class_uses($this->model)[SoftDeletes::class]);
    }
}