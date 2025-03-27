<?php

declare(strict_types=1);

namespace App\City\Domain;

use Ramsey\Uuid\Uuid;

final class CityFactory
{
    public function create(string $name, float $latitude, float $longitude): City
    {
        return new City(
            uuid: Uuid::uuid4(),
            name: new CityName($name),
            geolocation: new Geolocation($latitude, $longitude),
        );
    }
}