<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use JsonSerializable;
use Stringable;

abstract readonly class StringValueObject implements JsonSerializable, Stringable
{
    public function __construct(
        protected string $name,
    ) {}

    public static function from(?string $value): ?static
    {
        return is_null($value) ? null : new static($value);
    }

    public function equals(StringValueObject $stringValueObject): bool
    {
        return $this->value() === $stringValueObject->value();
    }

    public function value(): string
    {
        return $this->name;
    }

    public function jsonSerialize(): string
    {
        return $this->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }
}