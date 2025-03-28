<?php

declare(strict_types=1);

namespace App\City\Domain;

use App\Shared\Domain\Criteria\CompositeExpression\AndExpression;
use App\Shared\Domain\Criteria\CompositeExpression\OrExpression;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter\EqualTo;
use App\Shared\Domain\Criteria\Filter\NotEqualTo;

final readonly class DuplicatedUpdatedCitySpecification implements CitySpecification
{
    public function __construct(
        private City $city,
    ) {}

    public function isSatisfiedBy(City $city): bool
    {
        return !$this->city->uuid->equals($city->uuid)
            && ($this->city->name->equals($city->name) || $this->city->geolocation->equals($city->geolocation));
    }

    public function criteria(): Criteria
    {
        return new Criteria(
            expression: new AndExpression(
                new NotEqualTo('uuid', $this->city->uuid),
                new OrExpression(
                    new EqualTo(field: 'name', value: (string) $this->city->name),
                    new AndExpression(
                        new EqualTo(field: 'latitude', value: $this->city->geolocation->latitude),
                        new EqualTo(field: 'longitude', value: $this->city->geolocation->longitude),
                    ),
                ),
            ),
        );
    }
}