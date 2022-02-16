<?php

namespace BiiiiiigMonster\Cleans\Jobs;

use BiiiiiigMonster\Cleans\Cleaner;
use BiiiiiigMonster\Cleans\Contracts\CleansAttributes;
use BiiiiiigMonster\Cleans\Exceptions\NotAllowedCleansException;
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
     * @param CleansAttributes|null $condition
     * @param bool $cleanWithSoftDelete
     * @param bool $isForce
     */
    public function __construct(
        protected Model $model,
        protected string $relationName,
        protected ?CleansAttributes $condition = null,
        protected bool $cleanWithSoftDelete = true,
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
        $cleanModels = collect($this->model->getRelationValue($this->relationName))
            ->filter(
                static fn(Model $clean) => $this->condition instanceof CleansAttributes
                    ? !$this->condition->retain($clean, $this->model)
                    : ($this->isForce || !$this->retainedDuringSoftDeletes())
            );

        $relation = $this->model->{$this->relationName}();
        match ($relation::class) {
            HasOne::class, HasOneThrough::class, MorphOne::class,
            HasMany::class, HasManyThrough::class, MorphMany::class => $cleanModels->map(
                static fn(Model $relationModel) => $this->isForce
                    ? $relationModel->forceDelete()
                    : $relationModel->delete()
            ),
            BelongsToMany::class, MorphToMany::class => $relation->detach(
                $cleanModels->pluck($relation->getRelatedKeyName())
            ),
            default => throw new NotAllowedCleansException(sprintf(
                'The clean "%s::%s" is relationship of "%s", it not allowed to be cleaned.',
                $this->model,
                $this->relationName,
                $relation::class
            ))
        };
    }

    /**
     * Determine if the clean model retained during normal deleting.
     *
     * @return bool
     */
    protected function retainedDuringSoftDeletes(): bool
    {
        // The static model must have "SoftDeletes" trait and close propagate soft delete.
        return Cleaner::hasSoftDeletes($this->model) && !$this->cleanWithSoftDelete;
    }
}