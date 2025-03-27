<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper;

use App\Shared\Domain\Entity;
use Tempest\Mapper\Mapper;
use Tempest\Mapper\SerializerFactory;
use Tempest\Reflection\ClassReflector;
use Tempest\Reflection\PropertyReflector;

final readonly class EntityToPresenterMapper implements Mapper
{
    public function __construct(
        private SerializerFactory $serializerFactory,
    ) {}

    public function canMap(mixed $from, mixed $to): bool
    {
        return $from instanceof Entity && $to === 'array';
    }

    /**
     * This serializes objects into arrays but preserving the scalar values instead of converting them to strings.
     */
    public function map(mixed $from, mixed $to): array
    {
        $mappedProperties = [];
        $modelClass = new ClassReflector($from);

        foreach ($modelClass->getProperties() as $property) {
            $mappedProperties[$property->getName()] = $this->resolvePropertyValue($property, $from);
        }

        return $mappedProperties;
    }

    private function resolvePropertyValue(PropertyReflector $property, object $object): mixed
    {
        $propertyValue = $property->getValue($object);

        if ($propertyValue === null || $property->getType()->isScalar()) {
            return $propertyValue;
        }

        $serializer = $this->serializerFactory->forProperty($property);

        if ($serializer === null) {
            return $propertyValue;
        }

        return $serializer->serialize($propertyValue);
    }
}