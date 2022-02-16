<?php


namespace BiiiiiigMonster\Cleanable\Concerns;


use BiiiiiigMonster\Cleanable\Cleanabler;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait Cleanable
 *
 * @property array $cleanable The relationships that should be deleted when deleted.
 * @property bool $cleanWithSoftDelete Determine if propagate soft delete to cleanable.
 * @property string|null $cleanQueue Execute clean use the queue.
 * @package BiiiiiigMonster\Cleanable\Concerns
 */
trait Cleanable
{
    /**
     * Auto register cleanable.
     */
    protected static function bootCleanable(): void
    {
        static::deleted(static fn(Model $model) => Cleanabler::make($model)->clean());
        if (Cleanabler::hasSoftDeletes(static::class)) {
            static::forceDeleted(static fn(Model $model) => Cleanabler::make($model)->clean(true));
        }
    }

    /**
     * Get cleanable.
     *
     * @return array
     */
    public function getCleanable(): array
    {
        return $this->cleanable ?? [];
    }

    /**
     * Set the cleanable attributes for the model.
     *
     * @param array $cleanable
     * @return $this
     */
    public function setCleanable(array $cleanable): static
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
    public function makeCleanable(array|string|null $cleanables): static
    {
        $this->cleanable = array_merge(
            $this->getCleanable(), is_array($cleanables) ? $cleanables : func_get_args()
        );

        return $this;
    }

    /**
     * Make the given, typically visible, attributes cleanable if the given truth test passes.
     *
     * @param mixed $condition
     * @param array|string|null $cleanables
     * @return $this
     */
    public function makeCleanableIf(mixed $condition, array|string|null $cleanables): static
    {
        return value($condition, $this) ? $this->makeCleanable($cleanables) : $this;
    }

    /**
     * @return string|null
     */
    public function getCleanQueue(): ?string
    {
        return $this->cleanQueue;
    }

    /**
     * @param string|null $cleanQueue
     */
    public function setCleanQueue(?string $cleanQueue): void
    {
        $this->cleanQueue = $cleanQueue;
    }

    /**
     * Get cleanWithSoftDelete.
     *
     * @return bool
     */
    public function isCleanWithSoftDelete(): bool
    {
        return $this->cleanWithSoftDelete ?? true;
    }

    /**
     * Set the cleanWithSoftDelete attributes for the model.
     *
     * @param bool $cleanWithSoftDelete
     */
    public function setCleanWithSoftDelete(bool $cleanWithSoftDelete): void
    {
        $this->cleanWithSoftDelete = $cleanWithSoftDelete;
    }
}
