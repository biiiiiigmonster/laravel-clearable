<?php


namespace Biiiiiigmonster\Cleanable;


use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use ReflectionClass;

class Cleanabler
{
    /**
     * Observe model.
     *
     * @var Model
     */
    protected Model $model;

    /**
     * Cleanabler constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Make instance.
     *
     * @param Model $model
     * @return static
     */
    public static function make(Model $model): static
    {
        return new static($model);
    }

    /**
     * Cleanable handle.
     *
     * @param bool $isForce
     */
    public function handle(bool $isForce = false): void
    {
        $cleanable = $this->model->getCleanable();

        foreach ($cleanable as $relationName => $condition) {
            if (is_numeric($relationName)) {
                $relationName = $condition;
                $condition = null;
            }

            $this->clean($relationName, $condition, $isForce);
        }
    }

    /**
     * Clean the relation model.
     *
     * @param string $relationName
     * @param Closure|null $condition
     * @param bool $isForce
     */
    protected function clean(string $relationName, ?Closure $condition = null, bool $isForce = false): void
    {
        /** @var Relation $relation */
        $relation = $this->model->$relationName();

        // Filter the model's relation value to be clean up.
        $willClean = fn(string $relationName): Collection => collect($this->model->getRelationValue($relationName))
            ->filter(
                fn(Model $relationModel) => $condition instanceof Closure
                    ? $condition($this->model, $relationModel)
                    : ($isForce || !$this->isSoftDeleteModel() || $this->model->isPassSoftDeletedOn())
            );

        // Clean up according to relation.
        match ($relation::class) {
            HasOne::class, HasOneThrough::class, MorphOne::class,
            HasMany::class, HasManyThrough::class, MorphMany::class => $willClean($relationName)->map(
                fn(Model $relationModel) => $isForce ? $relationModel->forceDelete() : $relationModel->delete()
            ),
            BelongsToMany::class, MorphToMany::class => $relation->detach(
                $willClean($relationName)->pluck($relation->getRelatedKeyName())
            ),
        };
    }

    /**
     * Determine if the model use 'SoftDeletes' trait.
     *
     * @return bool
     */
    protected function isSoftDeleteModel(): bool
    {
        return in_array(SoftDeletes::class, (new ReflectionClass($this->model))->getTraitNames());
    }
}