<?php

namespace BiiiiiigMonster\Clearable;

use BiiiiiigMonster\Clearable\Attributes\Clear;
use BiiiiiigMonster\Clearable\Jobs\ClearsJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionMethod;

class ClearManager
{
    /**
     * ClearManager constructor.
     *
     * @param Model $model
     */
    public function __construct(
        protected Model $model
    ) {
    }

    /**
     * Make instance.
     *
     * @param Model $model
     * @return static
     */
    public static function make(Model $model): static
    {
        return new static($model);
    }

    /**
     * ClearManager handle.
     */
    public function handle(): void
    {
        $clears = $this->parse();

        foreach ($clears as $relationName => $clear) {
            $payload = [
                $this->model::class,
                $this->model->getOriginal(),
                $relationName,
                $relations = Collection::wrap($this->model->$relationName),
                $clear->clearsAttributesClassName
            ];

            if ($relations->isNotEmpty()) {
                match ($clear->clearQueue) {
                    null,false => ClearsJob::dispatchSync(...$payload),
                    true,'' => ClearsJob::dispatch(...$payload),
                    default => ClearsJob::dispatch(...$payload)->onQueue($clear->clearQueue)
                };
            }
        }
    }

    /**
     * Parse clears of the model.
     *
     * @return array<string, Clear>
     */
    protected function parse(): array
    {
        $clears = [];

        // from clears array
        foreach ($this->model->getClears() as $relationName => $clearsAttributesClassName) {
            if (is_numeric($relationName)) {
                $relationName = $clearsAttributesClassName;
                $clearsAttributesClassName = null;
            }

            $clears[$relationName] = new Clear($clearsAttributesClassName, $this->model->getClearQueue());
        }

        // from clear attribute
        $rfc = new ReflectionClass($this->model);
        $methods = $rfc->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (empty($clearAttributes = $method->getAttributes(Clear::class))) {
                continue;
            }

            $clears[$method->getName()] = $clearAttributes[0]->newInstance();
        }

        return $clears;
    }
}
