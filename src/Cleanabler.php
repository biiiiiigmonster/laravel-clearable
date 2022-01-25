<?php


namespace Biiiiiigmonster\Cleanable;


use Biiiiiigmonster\Cleanable\Attributes\Clean;
use Biiiiiigmonster\Cleanable\Jobs\CleanJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use LogicException;
use ReflectionClass;
use ReflectionMethod;

class Cleanabler
{
    /**
     * Cleanabler constructor.
     *
     * @param Model $model
     */
    public function __construct(
        protected Model $model
    )
    {
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
     * Cleanable handle.
     *
     * @param bool $isForce
     * @throws LogicException
     */
    public function clean(bool $isForce = false): void
    {
        $cleanable = $this->parse();

        foreach ($cleanable as $relationName => $configure) {
            $relation = $this->model->$relationName();
            if (!$relation instanceof Relation) {
                throw new LogicException(
                    sprintf('%s::%s must return a relationship instance.', $this->model::class, $relationName)
                );
            }

            $param = [$relationName, $configure->condition, $configure->propagateSoftDelete, $isForce];
            $configure->cleanQueue
                ? CleanJob::dispatch($this->model->withoutRelations(), ...$param)->onQueue($configure->cleanQueue)
                : CleanJob::dispatchSync($this->model, ...$param);
        }
    }

    /**
     * Parse cleanable of the model.
     *
     * @return array
     */
    protected function parse(): array
    {
        $cleanable = [];

        // from cleanable array
        foreach ($this->model->getCleanable() as $relationName => $configure) {
            if (is_numeric($relationName)) {
                $relationName = $configure;
                $configure = [];
            }

            $cleanable[$relationName] = new Clean(...(array)$configure);
        }

        // from clean attribute
        $rfc = new ReflectionClass($this->model);
        $methods = $rfc->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $cleanAttributes = $method->getAttributes(Clean::class);
            if (empty($cleanAttribute)) {
                continue;
            }

            $cleanable[$method->getName()] = $cleanAttributes[0]->newInstance();
        }

        return $cleanable;
    }
}