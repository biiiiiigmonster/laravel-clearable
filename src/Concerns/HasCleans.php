<?php


namespace BiiiiiigMonster\Cleans\Concerns;


use BiiiiiigMonster\Cleans\Cleaner;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasCleans
 *
 * @property array $cleans The relationships that will be auto-cleaned when deleted.
 * @package BiiiiiigMonster\Cleans\Concerns
 */
trait HasCleans
{
    /**
     * Auto register cleans.
     */
    protected static function bootHasCleans(): void
    {
        static::deleted(static fn(Model $model) => Cleaner::make($model)->handle());
        if (Cleaner::hasSoftDeletes(static::class)) {
            static::forceDeleted(static fn(Model $model) => Cleaner::make($model)->handle(true));
        }
    }

    /**
     * Get cleans.
     *
     * @return array
     */
    public function getCleans(): array
    {
        return $this->cleans ?? [];
    }

    /**
     * Set the cleans attributes for the model.
     *
     * @param array $cleans
     * @return $this
     */
    public function setCleans(array $cleans): static
    {
        $this->cleans = $cleans;

        return $this;
    }

    /**
     * Make the given, typically visible, attributes cleans.
     *
     * @param array|string|null $cleans
     * @return $this
     */
    public function clean(array|string|null $cleans): static
    {
        $this->cleans = array_merge(
            $this->getCleans(), is_array($cleans) ? $cleans : func_get_args()
        );

        return $this;
    }
}
