<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper;

use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use ReflectionProperty;
use Tempest\Database\DatabaseModel;
use Tempest\Mapper\Mapper;
use Tempest\Mapper\SerializerFactory;
use Tempest\Reflection\PropertyReflector;

use function Tempest\Support\str;

final readonly class EntityToModelMapper implements Mapper
{
    public function __construct(
        private SerializerFactory $serializerFactory,
    ) {}

    public function canMap(mixed $from, mixed $to): bool
    {
        return is_string($to) && is_a($to, DatabaseModel::class, allow_string: true);
    }

    /**
     * @throws ReflectionException
     */
    public function map(mixed $from, mixed $to): DatabaseModel
    {
        $entityReflectionObject = new ReflectionObject($from);
        $modelReflectionClass = new ReflectionClass($to);
        $modelInstance = $modelReflectionClass->newInstanceWithoutConstructor();

        foreach ($entityReflectionObject->getProperties() as $entityReflectionProperty) {
            if (!$entityReflectionProperty->isPublic()) {
                continue;
            }

            if ($this->setPropertyDirectly($from, $modelReflectionClass, $entityReflectionProperty, $modelInstance)) {
                continue;
            }

            foreach ($modelReflectionClass->getProperties() as $modelReflectionProperty) {
                if ($this->mapPropertyByAttribute($from, $entityReflectionObject, $modelReflectionProperty, $modelInstance)) {
                    continue;
                }

                if ($this->mapPropertyByNamingConvention($from, $entityReflectionProperty, $modelReflectionProperty, $modelInstance)) {
                    continue;
                }

                $this->mapValueObjectToProperty($from, $entityReflectionProperty, $modelReflectionProperty, $modelInstance);
            }
        }

        return $modelInstance;
    }

    /**
     * @throws ReflectionException
     */
    private function setPropertyDirectly(
        object $from,
        ReflectionClass $modelReflectionClass,
        ReflectionProperty $entityReflectionProperty,
        DatabaseModel $modelInstance,
    ): bool {
        if (!$modelReflectionClass->hasProperty($entityReflectionProperty->getName())) {
            return false;
        }

        $modelReflectionProperty = $modelReflectionClass->getProperty($entityReflectionProperty->getName());

        $modelReflectionProperty->setValue(
            $modelInstance,
            $this->getSerializedValue($from, $entityReflectionProperty)
        );

        return true;
    }

    private function getSerializedValue(object $from, ReflectionProperty $entityReflectionProperty): mixed
    {
        $value = $entityReflectionProperty->getValue($from);

        return $this
            ->serializerFactory
            ->forProperty(new PropertyReflector($entityReflectionProperty))
            ?->serialize($value) ?? $value;
    }

    private function mapPropertyByNamingConvention(
        object $from,
        ReflectionProperty $entityReflectionProperty,
        ReflectionProperty $modelReflectionProperty,
        DatabaseModel $modelInstance,
    ): bool {
        if (!str($entityReflectionProperty->getName())->snake()->equals($modelReflectionProperty->getName())) {
            return false;
        }

        if ($entityReflectionProperty->getType()->getName() !== $modelReflectionProperty->getType()->getName()) {
            return false;
        }

        $modelReflectionProperty->setValue($modelInstance, $entityReflectionProperty->getValue($from));

        return true;
    }

    private function mapPropertyByAttribute(
        object $from,
        ReflectionObject $entityReflectionObject,
        ReflectionProperty $modelReflectionProperty,
        DatabaseModel $modelInstance,
    ): bool {
        $propertyNameResolverAttribute = $modelReflectionProperty->getAttributes(PropertyNameResolver::class)[0] ?? null;

        if (is_null($propertyNameResolverAttribute)) {
            return false;
        }

        $original = $propertyNameResolverAttribute->getArguments()['original']
            ?? $propertyNameResolverAttribute->getArguments()[0]
            ?? null;

        if (is_null($original)) {
            return false;
        }

        if (!$entityReflectionObject->hasProperty($original)) {
            return false;
        }

        $entityReflectionProperty = $entityReflectionObject->getProperty($original);

        if ($entityReflectionProperty->getType()->getName() !== $modelReflectionProperty->getType()->getName()) {
            return false;
        }

        $modelReflectionProperty->setValue($modelInstance, $entityReflectionProperty->getValue($from));

        return true;
    }

    /**
     * @throws ReflectionException
     */
    private function mapValueObjectToProperty(
        object $from,
        ReflectionProperty $entityReflectionProperty,
        ReflectionProperty $modelReflectionProperty,
        DatabaseModel $modelInstance,
    ): void {
        $valueObjectResolverAttribute = $modelReflectionProperty->getAttributes(ValueObjectResolver::class)[0] ?? null;

        if (is_null($valueObjectResolverAttribute)) {
            return;
        }

        $valueObjectClassName = $valueObjectResolverAttribute->getArguments()['className'] ?? null;
        $valueObjectPropertyName = $valueObjectResolverAttribute->getArguments()['propertyName'] ?? null;

        if (!is_a($entityReflectionProperty->getValue($from), $valueObjectClassName, allow_string: true)) {
            return;
        }

        $valueObjectClass = new ReflectionClass($entityReflectionProperty->getValue($from));

        if (!$valueObjectClass->hasProperty($valueObjectPropertyName)) {
            return;
        }

        $valueObjectProperty = $valueObjectClass->getProperty($valueObjectPropertyName);

        if ($valueObjectProperty->getType()->getName() !== $modelReflectionProperty->getType()->getName()) {
            return;
        }

        $modelReflectionProperty->setValue(
            $modelInstance,
            $valueObjectClass->getProperty($valueObjectPropertyName)->getValue($entityReflectionProperty->getValue($from))
        );
    }
}