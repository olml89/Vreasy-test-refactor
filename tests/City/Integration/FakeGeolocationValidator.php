<?php

declare(strict_types=1);

namespace Tests\City\Integration;

use App\City\Domain\CityName;
use App\City\Domain\Geolocation;
use App\City\Domain\GeolocationValidator;
use App\City\Domain\InvalidGeolocationException;

final readonly class FakeGeolocationValidator implements GeolocationValidator
{
    public function __construct(
        private bool $throwException = false,
    ) {}

    public function validate(Geolocation $geolocation, CityName $cityName): void
    {
        if ($this->throwException) {
            throw new InvalidGeolocationException($geolocation, $cityName);
        }
    }
}