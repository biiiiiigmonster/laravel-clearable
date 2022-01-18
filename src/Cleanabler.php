<?php


namespace Biiiiiigmonster\Cleanable;


use Biiiiiigmonster\Cleanable\Attributes\Clean;
use Biiiiiigmonster\Cleanable\Exceptions\NotAllowedCleanableException;
use Biiiiiigmonster\Cleanable\Jobs\CleanJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
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
     * @throws NotAllowedCleanableException
     */
    public function clean(bool $isForce = false): void
    {
        $model = $this->model->withoutRelations();
        $cleanable = $this->parse();

        foreach ($cleanable as $relationName => $configure) {
            $relation = $model->$relationName();
            if (!$relation instanceof Relation) {
                throw new NotAllowedCleanableException(
                    sprintf('The cleanable "%s" is relation of "%s", it not allowed to be cleaned.', $relationName, $relation::class)
                );
            }

            [$condition, $propagateSoftDelete, $cleanQueue] = $configure;
            $cleanQueue
                ? CleanJob::dispatch($model, $relationName, $condition, $propagateSoftDelete, $isForce)->onQueue($cleanQueue)
                : CleanJob::dispatchSync($model, $relationName, $condition, $propagateSoftDelete, $isForce);
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
        foreach ($this->model->getCleanable() as $relationName => $condition) {
            if (is_numeric($relationName)) {
                $relationName = $condition;
                $condition = null;
            }

            $cleanable[$relationName] = [
                $condition,
                $this->model->isPropagateSoftDelete(),
                $this->model->getCleanQueue()
            ];
        }

        // from clean attribute
        $rfc = new ReflectionClass($this->model);
        $methods = $rfc->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            $cleanAttributes = $method->getAttributes(Clean::class);
            if (empty($cleanAttribute)) {
                continue;
            }

            /** @var Clean $instance */
            $instance = $cleanAttributes[0]->newInstance();
            $cleanable[$method->getName()] = [
                $instance->condition,
                $instance->propagateSoftDelete,
                $instance->cleanQueue,
            ];
        }

        return $cleanable;
    }
}