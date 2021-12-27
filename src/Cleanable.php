<?php


namespace Biiiiiigmonster\Cleanable;


use Closure;
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
     * Determine if pass soft deleted on to cleanable.
     *
     * @var bool
     */
    protected bool $passSoftDeletedOn = true;

    /**
     * Auto register cleanable.
     */
    protected static function bootCleanable(): void
    {
        static::deleted(fn (Model $model) => Cleanabler::make($model)->handle());
        static::forceDeleted(fn (Model $model) => Cleanabler::make($model)->handle(true));
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
     * @param bool|Closure $condition
     * @param array|string|null $cleanables
     * @return $this
     */
    public function makeCleanableIf(bool|Closure $condition, array|string|null $cleanables): static
    {
        $condition = $condition instanceof Closure ? $condition($this) : $condition;

        return value($condition) ? $this->makeCleanable($cleanables) : $this;
    }

    /**
     * Get passSoftDeletedOn.
     *
     * @return bool
     */
    public function isPassSoftDeletedOn(): bool
    {
        return $this->passSoftDeletedOn;
    }

    /**
     * Set the passSoftDeletedOn attributes for the model.
     *
     * @param bool $passSoftDeletedOn
     */
    public function setPassSoftDeletedOn(bool $passSoftDeletedOn): void
    {
        $this->passSoftDeletedOn = $passSoftDeletedOn;
    }
}
