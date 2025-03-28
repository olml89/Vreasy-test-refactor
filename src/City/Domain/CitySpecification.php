<?php

declare(strict_types=1);

namespace App\City\Domain;

use App\Shared\Domain\Criteria\Specification;

interface CitySpecification extends Specification
{
    public function isSatisfiedBy(City $city): bool;
}