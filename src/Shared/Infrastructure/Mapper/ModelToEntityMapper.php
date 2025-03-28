<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper;

use App\Shared\Domain\Entity;
use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use Tempest\Database\DatabaseModel;
use Tempest\Mapper\CasterFactory;
use Tempest\Mapper\Mapper;

final readonly class ModelToEntityMapper implements Mapper
{
    use IsPropertyMapper;

    public function __construct(
        private CasterFactory $casterFactory,
    ) {}

    public function canMap(mixed $from, mixed $to): bool
    {
        return $from instanceof DatabaseModel && is_string($to) && is_a($to, Entity::class, allow_string: true);
    }

    /**
     * @param DatabaseModel $from
     *
     * @throws ReflectionException
     */
    public function map(mixed $from, mixed $to): object
    {
        $modelReflectionObject = new ReflectionObject($from);
        $entityReflectionClass = new ReflectionClass($to);

        /** @var Entity $entity */
        $entity = $entityReflectionClass->newInstanceWithoutConstructor();

        foreach ($entityReflectionClass->getProperties() as $entityReflectionProperty) {
            if (!$entityReflectionProperty->isPublic()) {
                continue;
            }

            $entityPropertyWrapper = new EntityPropertyWrapper($entityReflectionProperty, $entity);

            if ($modelReflectionObject->hasProperty($entityReflectionProperty->getName())) {
                $modelReflectionProperty = $modelReflectionObject->getProperty($entityReflectionProperty->getName());
                $modelPropertyWrapper = new ModelPropertyWrapper($modelReflectionProperty, $from);
                $this->assignPropertyValue($entityPropertyWrapper, $modelPropertyWrapper);

                continue;
            }

            foreach ($modelReflectionObject->getProperties() as $modelReflectionProperty) {
                $modelPropertyWrapper = new ModelPropertyWrapper($modelReflectionProperty, $from);

                if ($this->mapPropertyByAttribute($entityPropertyWrapper, $modelPropertyWrapper)) {
                    continue 2;
                }

                if ($this->mapPropertyByNamingConvention($entityPropertyWrapper, $modelPropertyWrapper)) {
                    continue 2;
                }
            }

            $this->mapPropertiesToValueObject($entityPropertyWrapper, $modelReflectionObject, $from);
        }

        return $entity;
    }

    private function assignPropertyValue(
        EntityPropertyWrapper $entityPropertyWrapper,
        ModelPropertyWrapper $modelPropertyWrapper,
    ): void {
        $value = $entityPropertyWrapper->hasSameType($modelPropertyWrapper)
            ? $modelPropertyWrapper->getValue()
            : $modelPropertyWrapper->cast($this->casterFactory, $entityPropertyWrapper);

        $entityPropertyWrapper->setValue($value);
    }

    /**
     * @throws ReflectionException
     */
    public function mapPropertiesToValueObject(
        EntityPropertyWrapper $entityPropertyWrapper,
        ReflectionObject $modelReflectionObject,
        DatabaseModel $model,
    ): void {
        if (!$entityPropertyWrapper->classExists()) {
            return;
        }

        $valueObjectReflectionClass = new ReflectionClass($entityPropertyWrapper->property()->getType()->getName());
        $valueObject = $valueObjectReflectionClass->newInstanceWithoutConstructor();

        foreach ($modelReflectionObject->getProperties() as $modelReflectionProperty) {
            $modelPropertyWrapper = new ModelPropertyWrapper($modelReflectionProperty, $model);

            if (is_null($valueObjectResolverAttribute = $this->getValueObjectResolverAttribute($modelPropertyWrapper))) {
                continue;
            }

            $valueObjectClassName = $valueObjectResolverAttribute->getArguments()['className'] ?? null;
            $valueObjectPropertyName = $valueObjectResolverAttribute->getArguments()['propertyName'] ?? null;

            if (!$entityPropertyWrapper->isClass($valueObjectClassName)) {
                continue;
            }

            if (!$valueObjectReflectionClass->hasProperty($valueObjectPropertyName)) {
                continue;
            }

            $valueObjectReflectionProperty = $valueObjectReflectionClass->getProperty($valueObjectPropertyName);

            if (!$modelPropertyWrapper->hasSameType($valueObjectReflectionProperty)) {
                continue;
            }

            $value = $modelPropertyWrapper->getValue();
            $valueObjectReflectionProperty->setValue($valueObject, $value);
        }

        $entityPropertyWrapper->setValue($valueObject);
    }
}