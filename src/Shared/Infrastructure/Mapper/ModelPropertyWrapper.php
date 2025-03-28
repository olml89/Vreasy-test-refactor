<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper;

use ReflectionProperty;
use Tempest\Database\DatabaseModel;
use Tempest\Reflection\PropertyReflector;

final readonly class ModelPropertyWrapper implements PropertyWrapper
{
    use IsPropertyWrapper;

    private DatabaseModel $model;

    public function __construct(ReflectionProperty $reflectionProperty, DatabaseModel $model)
    {
        $this->propertyReflector = new PropertyReflector($reflectionProperty);
        $this->model = $model;
    }

    public function model(): DatabaseModel
    {
        return $this->model;
    }
    public function getValue(): mixed
    {
        return $this->property()->getValue($this->model);
    }

    public function setValue(mixed $value): void
    {
        $this->property()->setValue($this->model, $value);
    }
}