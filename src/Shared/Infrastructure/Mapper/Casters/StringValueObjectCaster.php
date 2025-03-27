<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper\Casters;

use Tempest\Mapper\Caster;
use Tempest\Reflection\PropertyReflector;

final readonly class StringValueObjectCaster implements Caster
{
    public function __construct(
        private PropertyReflector $propertyReflector,
    ) {}

    public static function fromProperty(PropertyReflector $propertyReflector): self
    {
        return new self($propertyReflector);
    }

    public function cast(mixed $input): mixed
    {
        $className = $this->propertyReflector->getType()->asClass()->getName();

        return new $className($input);
    }
}