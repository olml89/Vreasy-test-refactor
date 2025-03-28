<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper;

use App\Shared\Domain\Entity;
use ReflectionProperty;
use Tempest\Reflection\PropertyReflector;

final readonly class EntityPropertyWrapper implements PropertyWrapper
{
    use IsPropertyWrapper;

    private Entity $entity;

    public function __construct(ReflectionProperty $reflectionProperty, Entity $entity)
    {
        $this->propertyReflector = new PropertyReflector($reflectionProperty);
        $this->entity = $entity;
    }

    public function entity(): Entity
    {
        return $this->entity;
    }

    public function getValue(): mixed
    {
        return $this->property()->getValue($this->entity);
    }

    public function setValue(mixed $value): void
    {
        $this->property()->setValue($this->entity, $value);
    }
}