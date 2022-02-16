<?php


namespace BiiiiiigMonster\Cleans\Concerns;


use BiiiiiigMonster\Cleans\Cleaner;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait HasCleans
 *
 * @property array $cleans The relationships that should be deleted when deleted.
 * @property bool $cleanWithSoftDelete Determine if propagate soft delete to cleans.
 * @property string|null $cleanQueue Execute clean use the queue.
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
