<?php

namespace BiiiiiigMonster\Clearable\Jobs;

use BiiiiiigMonster\Clearable\Exceptions\NotAllowedClearsException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use LogicException;

class ClearsJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;
    use SerializesModels;
    use InteractsWithQueue;

    /**
     * ClearJob constructor.
     *
     * @param string $className
     * @param array $original
     * @param string $relationName
     * @param Collection $collection
     * @param string|null $invokableClearClassName
     */
    public function __construct(
        protected string $className,
        protected array $original,
        protected string $relationName,
        protected Collection $collection,
        protected ?string $invokableClearClassName = null,
    ) {
    }

    /**
     * ClearJob handle.
     *
     * @throws NotAllowedClearsException
     */
    public function handle(): void
    {
        $model = (new $this->className())->setRawAttributes($this->original);
        $relation = $model->{$this->relationName}();

        // to be cleared model.
        $clears = $this->collection;
        if ($this->invokableClearClassName) {
            $invoke = new $this->invokableClearClassName();
            $clears = $clears->filter(fn (Model $clear) => $invoke($clear, $model));
        }

        switch (true) {
            case $relation instanceof HasOneOrMany:
                $clears->map(fn (Model $clear) => $clear->delete());
                break;
            case $relation instanceof BelongsToMany:
                $relation->detach($clears->pluck($relation->getRelatedKeyName())->all());
                break;
            case $relation instanceof BelongsTo:
                throw new NotAllowedClearsException(sprintf(
                    '%s::%s is relationship of %s, it not allowed to be cleared.',
                    $this->className,
                    $this->relationName,
                    $relation::class
                ));
            default:
                throw new LogicException(sprintf(
                    '%s::%s must return a relationship instance.',
                    $this->className,
                    $this->relationName
                ));
        }
    }
}
