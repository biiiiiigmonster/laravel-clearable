<?php


namespace BiiiiiigMonster\Cleans;


use BiiiiiigMonster\Cleans\Attributes\Clean;
use BiiiiiigMonster\Cleans\Jobs\CleansJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;
use LogicException;
use ReflectionClass;
use ReflectionMethod;

class Cleaner
{
    /**
     * Cleaner constructor.
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
     * Cleaner handle.
     *
     * @param bool $isForce
     * @throws LogicException
     */
    public function handle(bool $isForce = false): void
    {
        $cleans = $this->parse();

        foreach ($cleans as $relationName => $configure) {
            $relation = $this->model->$relationName();
            if (!$relation instanceof Relation) {
                throw new LogicException(
                    sprintf('%s::%s must return a relationship instance.', $this->model::class, $relationName)
                );
            }

            /** @var Clean $configure */
            $cleansAttributes = $configure->cleansAttributesClassName ? new $configure->cleansAttributesClassName() : null;
            $param = [$this->model, $relationName, $cleansAttributes, $configure->cleanWithSoftDelete, $isForce];
            $configure->cleanQueue
                ? CleansJob::dispatch(...$param)->onQueue($configure->cleanQueue)
                : CleansJob::dispatchSync(...$param);
        }
    }

    /**
     * Parse cleans of the model.
     *
     * @return array
     */
    protected function parse(): array
    {
        $cleans = [];

        // from cleans array
        foreach ($this->model->getCleans() as $relationName => $configure) {
            if (is_numeric($relationName)) {
                $relationName = $configure;
                $configure = [null];
            }

            $cleans[$relationName] = new Clean(...(array)$configure);
        }

        // from clean attribute
        $rfc = new ReflectionClass($this->model);
        $methods = $rfc->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (empty($cleanAttributes = $method->getAttributes(Clean::class))) {
                continue;
            }

            $cleans[$method->getName()] = $cleanAttributes[0]->newInstance();
        }

        return $cleans;
    }

    /**
     * Determine if the model has soft delete.
     *
     * @param Model|string $model
     * @return bool
     */
    public static function hasSoftDeletes(Model|string $model): bool
    {
        return isset(class_uses($model)[SoftDeletes::class]);
    }
}