<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use Stringable;

abstract readonly class StringValueObject implements Stringable
{
    public function __construct(
        protected string $name,
    ) {}

    public function value(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->value();
    }
}