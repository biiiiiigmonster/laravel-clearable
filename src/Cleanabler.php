<?php


namespace Biiiiiigmonster\Cleanable;


use Biiiiiigmonster\Cleanable\Contracts\CleanableAttributes;
use Biiiiiigmonster\Cleanable\Exceptions\NotAllowedCleanableException;
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
     * @throws NotAllowedCleanableException
     */
    public function handle(bool $isForce = false): void
    {
        $cleanable = $this->model->getCleanable();

        foreach ($cleanable as $relationName => $condition) {
            if (is_numeric($relationName)) {
                $relationName = $condition;
            }

            $cleanableModels = collect($this->model->getRelationValue($relationName))
                ->filter(
                    static fn(Model $relationModel): bool => $condition instanceof CleanableAttributes
                        ? $condition->decide($this->model, $relationModel)
                        : ($isForce || $this->model->isPropagateSoftDelete() || !$this->isSoftDeleteModel())
                );

            $relation = $this->model->$relationName();
            match ($relation::class) {
                HasOne::class, HasOneThrough::class, MorphOne::class,
                HasMany::class, HasManyThrough::class, MorphMany::class => $cleanableModels->map(
                    static fn(Model $relationModel): ?bool => $isForce
                        ? $relationModel->forceDelete()
                        : $relationModel->delete()
                ),
                BelongsToMany::class, MorphToMany::class => $relation->detach(
                    $cleanableModels->pluck($relation->getRelatedKeyName())
                ),
                default => throw new NotAllowedCleanableException(
                    sprintf('The cleanable "%s" is relation of "%s", it not allowed to be cleaned.', $relationName, $relation::class)
                )
            };
        }
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