<?php

namespace BiiiiiigMonster\Clears;

use BiiiiiigMonster\Clears\Attributes\Clear;
use BiiiiiigMonster\Clears\Jobs\ClearsJob;
use Illuminate\Database\Eloquent\Model;
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
            $param = [$relationName, $clear->clearsAttributesClassName];
            $clear->clearQueue
                ? ClearsJob::dispatch($this->model->withoutRelations(), ...$param)->onQueue($clear->clearQueue)
                : ClearsJob::dispatchSync($this->model, ...$param);
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
