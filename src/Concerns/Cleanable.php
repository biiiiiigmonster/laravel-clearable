<?php


namespace Biiiiiigmonster\Cleanable\Concerns;


use Biiiiiigmonster\Cleanable\Cleanabler;
use Illuminate\Database\Eloquent\Model;

trait Cleanable
{
    /**
     * The relationships that should be deleted when deleted.
     *
     * @var array
     */
    protected $cleanable = [];

    /**
     * Determine if propagate soft delete to cleanable.
     *
     * @var bool
     */
    protected $propagateSoftDelete = true;

    /**
     * Execute clean use the queue.
     *
     * @var string|null
     */
    protected $cleanQueue = null;

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
        return $this->cleanable;
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
            $this->cleanable, is_array($cleanables) ? $cleanables : func_get_args()
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
     * Get propagateSoftDelete.
     *
     * @return bool
     */
    public function isPropagateSoftDelete(): bool
    {
        return $this->propagateSoftDelete;
    }

    /**
     * Set the propagateSoftDelete attributes for the model.
     *
     * @param bool $propagateSoftDelete
     */
    public function setPropagateSoftDelete(bool $propagateSoftDelete): void
    {
        $this->propagateSoftDelete = $propagateSoftDelete;
    }
}
