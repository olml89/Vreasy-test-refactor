<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper;

use ReflectionClass;
use ReflectionException;
use ReflectionObject;
use ReflectionProperty;
use Tempest\Database\DatabaseModel;
use Tempest\Mapper\CasterFactory;
use Tempest\Mapper\Mapper;
use Tempest\Reflection\PropertyReflector;

final readonly class ModelToEntityMapper implements Mapper
{
    public function __construct(
        private CasterFactory $casterFactory,
    ) {}
    public function canMap(mixed $from, mixed $to): bool
    {
        return $from instanceof DatabaseModel;
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
        $entityInstance = $entityReflectionClass->newInstanceWithoutConstructor();

        foreach ($entityReflectionClass->getProperties() as $entityReflectionProperty) {
            if (!$entityReflectionProperty->isPublic()) {
                continue;
            }

            if ($this->setPropertyIfSettable($from, $modelReflectionObject, $entityReflectionProperty, $entityInstance)) {
                //continue;
            }
        }

        return $entityInstance;
    }

    /**
     * @throws ReflectionException
     */
    private function setPropertyIfSettable(
        DatabaseModel $from,
        ReflectionObject $modelReflectionObject,
        ReflectionProperty $entityReflectionProperty,
        object $entityInstance,
    ): bool {
        if (!$modelReflectionObject->hasProperty($entityReflectionProperty->getName())) {
            return false;
        }

        $modelReflectionProperty = $modelReflectionObject->getProperty($entityReflectionProperty->getName());

        $entityReflectionProperty->setValue(
            $entityInstance,
            $this->getCastedValue($from, $modelReflectionProperty, $entityReflectionProperty)
        );

        return true;
    }

    private function getCastedValue(DatabaseModel $from, ReflectionProperty $modelReflectionProperty, ReflectionProperty $entityReflectionProperty): mixed
    {
        $value = $modelReflectionProperty->getValue($from);

        return $this
            ->casterFactory
            ->forProperty(new PropertyReflector($entityReflectionProperty))
            ?->cast($value) ?? $value;
    }
}