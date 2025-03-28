<?php

declare(strict_types=1);

namespace App\City\Domain;

use App\Shared\Domain\UuidGenerator;

final readonly class CityFactory
{
    public function __construct(
        private UuidGenerator $uuidGenerator,
    ) {}

    public function create(CityName $cityName, Geolocation $geolocation): City
    {
        return new City(
            uuid: $this->uuidGenerator->random(),
            name:$cityName,
            geolocation: $geolocation,
        );
    }
}