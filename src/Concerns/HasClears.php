<?php

namespace BiiiiiigMonster\Clears\Concerns;

use BiiiiiigMonster\Clears\ClearManager;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasClears
 *
 * @property array $clears The relationships that will be auto-cleared when deleted.
 * @package BiiiiiigMonster\Clears\Concerns
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
}
