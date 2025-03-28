<?php

declare(strict_types=1);

namespace App\City\Domain;

use App\Shared\Domain\Criteria\CompositeExpression\AndExpression;
use App\Shared\Domain\Criteria\CompositeExpression\OrExpression;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter\EqualTo;

final readonly class DuplicatedNewCitySpecification implements CitySpecification
{
    public function __construct(
        private CityName $name,
        private Geolocation $geolocation,
    ) {}

    public function isSatisfiedBy(City $city): bool
    {
        return $this->name->equals($city->name) || $this->geolocation->equals($city->geolocation);
    }

    public function criteria(): Criteria
    {
        return new Criteria(
            expression: new OrExpression(
                new EqualTo(field: 'name', value: (string) $this->name),
                new AndExpression(
                    new EqualTo(field: 'latitude', value: $this->geolocation->latitude),
                    new EqualTo(field: 'longitude', value: $this->geolocation->longitude),
                )
            ),
        );
    }
}