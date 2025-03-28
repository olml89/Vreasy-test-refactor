<?php

declare(strict_types=1);

namespace Tests\City;

use App\City\Domain\City;
use App\City\Domain\CityName;
use App\City\Domain\Geolocation;
use Ramsey\Uuid\Uuid;

final class CityFactory
{
    public static function create(string $name, float $latitude, float $longitude): City
    {
        return new City(
            uuid: Uuid::uuid4(),
            name: new CityName($name),
            geolocation: new Geolocation($latitude, $longitude),
        );
    }
}