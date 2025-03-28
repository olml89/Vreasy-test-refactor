<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper;

use App\Shared\Domain\Entity;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use Tempest\Database\DatabaseModel;
use Tempest\Mapper\Mapper;
use Tempest\Mapper\SerializerFactory;

final readonly class EntityToModelMapper implements Mapper
{
    use IsPropertyMapper;

    public function __construct(
        private SerializerFactory $serializerFactory,
    ) {}

    public function canMap(mixed $from, mixed $to): bool
    {
        return $from instanceof Entity && is_string($to) && is_a($to, DatabaseModel::class, allow_string: true);
    }

    /**
     * @param Entity $from
     *
     * @throws ReflectionException
     */
    public function map(mixed $from, mixed $to): DatabaseModel
    {
        $entityReflectionObject = new ReflectionObject($from);
        $modelReflectionClass = new ReflectionClass($to);

        /** @var DatabaseModel $model */
        $model = $modelReflectionClass->newInstanceWithoutConstructor();

        foreach ($entityReflectionObject->getProperties() as $entityReflectionProperty) {
            if (!$entityReflectionProperty->isPublic()) {
                continue;
            }

            $entityPropertyWrapper = new EntityPropertyWrapper($entityReflectionProperty, $from);

            if ($modelReflectionClass->hasProperty($entityReflectionProperty->getName())) {
                $modelReflectionProperty = $modelReflectionClass->getProperty($entityReflectionProperty->getName());
                $modelPropertyWrapper = new ModelPropertyWrapper($modelReflectionProperty, $model);
                $this->assignPropertyValue($entityPropertyWrapper, $modelPropertyWrapper);

                continue;
            }

            foreach ($modelReflectionClass->getProperties() as $modelReflectionProperty) {
                $modelPropertyWrapper = new ModelPropertyWrapper($modelReflectionProperty, $model);

                if ($this->mapPropertyByAttribute($entityPropertyWrapper, $modelPropertyWrapper)) {
                    continue 2;
                }

                if ($this->mapPropertyByNamingConvention($entityPropertyWrapper, $modelPropertyWrapper)) {
                    continue 2;
                }

                $this->mapValueObjectToProperty($entityPropertyWrapper, $modelPropertyWrapper);
            }
        }

        return $model;
    }

    private function assignPropertyValue(
        EntityPropertyWrapper $entityPropertyWrapper,
        ModelPropertyWrapper $modelPropertyWrapper,
    ): void {
        $value = $entityPropertyWrapper->hasSameType($modelPropertyWrapper)
            ? $entityPropertyWrapper->getValue()
            : $entityPropertyWrapper->serialize($this->serializerFactory, $modelPropertyWrapper);

        $modelPropertyWrapper->setValue($value);
    }

    /**
     * @throws ReflectionException
     */
    private function mapValueObjectToProperty(
        EntityPropertyWrapper $entityPropertyWrapper,
        ModelPropertyWrapper $modelPropertyWrapper,
    ): void {
        if (!$entityPropertyWrapper->classExists()) {
            return;
        }

        if (is_null($valueObjectResolverAttribute = $this->getValueObjectResolverAttribute($modelPropertyWrapper))) {
            return;
        }

        $valueObjectClassName = $valueObjectResolverAttribute->getArguments()['className'] ?? null;
        $valueObjectPropertyName = $valueObjectResolverAttribute->getArguments()['propertyName'] ?? null;

        if (!$entityPropertyWrapper->isClass($valueObjectClassName)) {
            return;
        }

        $valueObjectClass = new ReflectionClass($entityPropertyWrapper->getValue());

        if (!$valueObjectClass->hasProperty($valueObjectPropertyName)) {
            return;
        }

        $valueObjectProperty = $valueObjectClass->getProperty($valueObjectPropertyName);

        if (!$modelPropertyWrapper->hasSameType($valueObjectProperty)) {
            return;
        }

        $value = $valueObjectProperty->getValue($entityPropertyWrapper->getValue());
        $modelPropertyWrapper->setValue($value);
    }
}