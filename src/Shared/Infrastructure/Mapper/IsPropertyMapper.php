<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper;

use ReflectionAttribute;

trait IsPropertyMapper
{
    abstract protected function assignPropertyValue(
        EntityPropertyWrapper $entityPropertyWrapper,
        ModelPropertyWrapper $modelPropertyWrapper,
    ): void;

    private function getValueObjectResolverAttribute(PropertyWrapper $propertyWrapper): ?ReflectionAttribute
    {
        return $propertyWrapper
            ->property()
            ->getAttributes(ValueObjectResolver::class)[0] ?? null;
    }

    private function mapPropertyByAttribute(
        EntityPropertyWrapper $entityPropertyWrapper,
        ModelPropertyWrapper $modelPropertyWrapper,
    ): bool {
        $propertyNameResolverAttribute = $modelPropertyWrapper
            ->property()
            ->getAttributes(PropertyNameResolver::class)[0] ?? null;

        if (is_null($propertyNameResolverAttribute)) {
            return false;
        }

        $original = $propertyNameResolverAttribute->getArguments()['original']
            ?? $propertyNameResolverAttribute->getArguments()[0]
            ?? null;

        if (is_null($original)) {
            return false;
        }

        if (!$entityPropertyWrapper->getName()->equals($original)) {
            return false;
        }

        $this->assignPropertyValue($entityPropertyWrapper, $modelPropertyWrapper);

        return true;
    }

    private function mapPropertyByNamingConvention(
        EntityPropertyWrapper $entityPropertyWrapper,
        ModelPropertyWrapper $modelPropertyWrapper,
    ): bool {
        if (!$modelPropertyWrapper->getName()->equals($entityPropertyWrapper->getName()->snake())) {
            return false;
        }

        $this->assignPropertyValue($entityPropertyWrapper, $modelPropertyWrapper);

        return true;
    }
}