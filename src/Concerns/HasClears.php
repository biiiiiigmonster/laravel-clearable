<?php

namespace BiiiiiigMonster\Clearable\Concerns;

use BiiiiiigMonster\Clearable\ClearManager;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasClears
 *
 * @property array $clears The relationships that will be auto-cleared when deleted.
 * @property ?string $clearQueue The clearable that will be dispatch on this name queue.
 */
trait HasClears
{
    /**
     * Auto register clears.
     */
    protected static function bootHasClears(): void
    {
        static::deleted(static fn (Model $model) => ClearManager::make($model)->handle());
    }

    /**
     * Get clears.
     *
     * @return array
     */
    public function getClears(): array
    {
        return $this->clears ?? [];
    }

    /**
     * Set the clears attributes for the model.
     *
     * @param array $clears
     * @return $this
     */
    public function setClears(array $clears): static
    {
        $this->clears = $clears;

        return $this;
    }

    /**
     * Make the given, typically visible, attributes clears.
     *
     * @param array|string|null $clears
     * @return $this
     */
    public function clear(array|string|null $clears): static
    {
        $this->clears = array_merge(
            $this->getClears(),
            is_array($clears) ? $clears : func_get_args()
        );

        return $this;
    }

    /**
     * Get clearQueue.
     *
     * @return string|null
     */
    public function getClearQueue(): ?string
    {
        return $this->clearQueue;
    }

    /**
     * Set the clearQueue attributes for the model.
     *
     * @param string|null $clearQueue
     * @return $this
     */
    public function setClearQueue(?string $clearQueue): static
    {
        $this->clearQueue = $clearQueue;

        return $this;
    }
}
