<?php


namespace Biiiiiigmonster\Cleanable;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
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
     * Eventabler constructor.
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

    public function handle(bool $isForce = false): void
    {
        if (!$isForce && $this->isSoftDeleteModel()) {
            return;
        }

        $cleanable = $this->model->getCleanable();
        foreach ($cleanable as $relationName) {
            $relation = $this->model->$relationName();

            if (
                $relation instanceof HasOne
                || $relation instanceof HasOneThrough
                || $relation instanceof MorphOne
            ) {
                $isForce
                    ? $this->model->getRelationValue($relationName)?->forceDelete()
                    : $this->model->getRelationValue($relationName)?->delete();
            }
            if (
                $relation instanceof HasMany
                || $relation instanceof HasManyThrough
                || $relation instanceof MorphMany
            ) {
                $this->model->getRelationValue($relationName)->map(
                    fn(Model $model) => $isForce ? $model->forceDelete() :$model->delete()
                );
            }
            if ($relation instanceof BelongsToMany) {//BelongsToMany or MorphToMany
                $relation->detach();
            }
        }
    }

    /**
     * 判断当前模型是否为软删除模型
     *
     * @return bool
     */
    private function isSoftDeleteModel(): bool
    {
        return in_array(SoftDeletes::class, (new ReflectionClass($this->model))->getTraitNames());
    }
}