<?php

declare(strict_types=1);

namespace App\City\Domain;

use App\Shared\Domain\StringValueObject;
use InvalidArgumentException;

final readonly class CityName extends StringValueObject
{
    public function __construct(string $name)
    {
        parent::__construct($name);

        if (mb_strlen($this->name) < 2) {
            throw new InvalidArgumentException('City name must be at least 2 characters long');
        }

        if (mb_strlen($this->name) > 100) {
            throw new InvalidArgumentException('City name must not exceed 100 characters long');
        }
    }
}