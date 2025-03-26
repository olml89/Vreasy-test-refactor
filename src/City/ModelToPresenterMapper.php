<?php

declare(strict_types=1);

namespace App\City;

use Tempest\Database\DatabaseModel;
use Tempest\Database\Id;
use Tempest\Mapper\Mapper;
use Tempest\Mapper\SerializerFactory;
use Tempest\Reflection\ClassReflector;
use Tempest\Reflection\PropertyReflector;

final readonly class ModelToPresenterMapper implements Mapper
{
    public function __construct(
        private SerializerFactory $serializerFactory,
    ) {}

    public function canMap(mixed $from, mixed $to): bool
    {
        return $from instanceof DatabaseModel && $to === 'array';
    }

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

        if ($property->getType()->matches(Id::class)) {
            return $propertyValue->id;
        }

        $serializer = $this->serializerFactory->forProperty($property);

        if ($serializer === null) {
            return $propertyValue;
        }

        return $serializer->serialize($propertyValue);
    }
}