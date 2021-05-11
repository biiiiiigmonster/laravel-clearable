<?php


namespace Biiiiiigmonster\Cleanable;


use Closure;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * 关联数据可清除
 * @package App\Concerns
 */
trait Cleanable
{
    /**
     * 自动注册
     */
    protected static function bootCleanable(): void
    {
        /** 针对10种不同的关联关系，严格意义上分为删除前处理和删除后处理，因此分别监听事件deleting,deleted */
        static::deleting(function ($model) {
            $cleanable = $model->getCleanable();
            foreach ($cleanable as $relationName) {
                $relation = $model->$relationName();
                if (!$relation instanceof Relation) continue;

                if ($relation instanceof BelongsTo) {//BelongsTo or MorphTo
                    $model->getRelationValue($relationName)?->delete();
                }
            }
        });
        static::deleted(function ($model) {
            $cleanable = $model->getCleanable();
            foreach ($cleanable as $relationName) {
                $relation = $model->$relationName();
                if (!$relation instanceof Relation) continue;

                if (
                    $relation instanceof HasOne
                    || $relation instanceof HasOneThrough
                    || $relation instanceof MorphOne
                ) {
                    $model->getRelationValue($relationName)?->delete();
                }
                if (
                    $relation instanceof HasMany
                    || $relation instanceof HasManyThrough
                    || $relation instanceof MorphMany
                ) {
                    $model->getRelationValue($relationName)->map(function ($item) {
                        $item->delete();
                    });
                }
                if ($relation instanceof BelongsToMany) {//BelongsToMany or MorphToMany
                    $relation->detach();
                }
            }
        });
    }

    /**
     * Get cleanable
     * @return array
     */
    public function getCleanable(): array
    {
        return $this->cleanable;
    }

    /**
     * Set the cleanable attributes for the model.
     *
     * @param array $cleanable
     * @return $this
     */
    public function setCleanable(array $cleanable)
    {
        $this->cleanable = $cleanable;

        return $this;
    }

    /**
     * Make the given, typically visible, attributes cleanable.
     *
     * @param array|string|null $cleanables
     * @return $this
     */
    public function makeCleanable($cleanables)
    {
        $this->cleanable = array_merge(
            $this->cleanable, is_array($cleanables) ? $cleanables : func_get_args()
        );

        return $this;
    }

    /**
     * Make the given, typically visible, attributes cleanable if the given truth test passes.
     *
     * @param bool|Closure $condition
     * @param array|string|null $cleanables
     * @return $this
     */
    public function makeCleanableIf($condition, $cleanables)
    {
        $condition = $condition instanceof Closure ? $condition($this) : $condition;

        return value($condition) ? $this->makeCleanable($cleanables) : $this;
    }
}
