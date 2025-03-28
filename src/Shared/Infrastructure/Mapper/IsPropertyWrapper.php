<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper;

use ReflectionProperty;
use Tempest\Mapper\CasterFactory;
use Tempest\Mapper\SerializerFactory;
use Tempest\Reflection\PropertyReflector;

use Tempest\Support\Str\ImmutableString;

use function Tempest\Support\str;

trait IsPropertyWrapper
{
    private readonly PropertyReflector $propertyReflector;

    public function property(): ReflectionProperty
    {
        return $this->propertyReflector->getReflection();
    }

    public function propertyReflector(): PropertyReflector
    {
        return $this->propertyReflector;
    }

    public function getName(): ImmutableString
    {
        return str($this->property()->getName());
    }

    public function classExists(): bool
    {
        return class_exists($this->property()->getType()->getName());
    }

    public function isClass(string $className): bool
    {
        return is_a($this->property()->getType()->getName(), $className, allow_string: true);
    }

    public function hasSameType(PropertyWrapper|ReflectionProperty $other): bool
    {
        $otherReflectionProperty = $other instanceof ReflectionProperty ? $other : $other->property();

        return $this->property()->getType()->getName() === $otherReflectionProperty->getType()->getName();
    }

    public function cast(CasterFactory $casterFactory, PropertyWrapper $to): mixed
    {
        return $casterFactory
            ->forProperty($to->propertyReflector())
            ?->cast($this->getValue());
    }

    public function serialize(SerializerFactory $serializerFactory, PropertyWrapper $to): array|string|null
    {
        return $serializerFactory
            ->forProperty($to->propertyReflector())
            ?->serialize($this->getValue());
    }
}