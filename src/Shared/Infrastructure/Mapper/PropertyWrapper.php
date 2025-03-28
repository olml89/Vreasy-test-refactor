<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper;

use ReflectionProperty;
use Tempest\Mapper\CasterFactory;
use Tempest\Mapper\SerializerFactory;
use Tempest\Reflection\PropertyReflector;
use Tempest\Support\Str\ImmutableString;

interface PropertyWrapper
{
    public function property(): ReflectionProperty;
    public function propertyReflector(): PropertyReflector;
    public function getName(): ImmutableString;
    public function classExists(): bool;
    public function isClass(string $className): bool;
    public function hasSameType(PropertyWrapper|ReflectionProperty $other): bool;
    public function getValue(): mixed;
    public function setValue(mixed $value): void;
    public function cast(CasterFactory $casterFactory, PropertyWrapper $to): mixed;
    public function serialize(SerializerFactory $serializerFactory, PropertyWrapper $to): array|string|null;
}