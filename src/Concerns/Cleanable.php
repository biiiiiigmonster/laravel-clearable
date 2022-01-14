<?php


namespace Biiiiiigmonster\Cleanable\Concerns;


use Biiiiiigmonster\Cleanable\Cleanabler;
use Biiiiiigmonster\Cleanable\Exceptions\NotAllowedCleanableException;
use Illuminate\Database\Eloquent\Model;

trait Cleanable
{
    /**
     * The relationships that should be deleted when deleted.
     *
     * @var array
     */
    protected array $cleanable = [];

    /**
     * Determine if propagate soft delete to cleanable.
     *
     * @var bool
     */
    protected bool $propagateSoftDelete = true;

    /**
     * Auto register cleanable.
     * @throws NotAllowedCleanableException
     */
    protected static function bootCleanable(): void
    {
        static::deleted(
            static function (Model $model): void {
                Cleanabler::make($model)->handle();
            }
        );
        static::forceDeleted(
            static function (Model $model): void {
                Cleanabler::make($model)->handle(true);
            }
        );
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
