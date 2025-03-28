<?php

declare(strict_types=1);

namespace App\City\Application;

use App\City\Domain\City;
use App\City\Domain\CityName;
use App\City\Domain\CityNotFoundException;
use App\City\Domain\CityRepository;
use App\City\Domain\Geolocation;
use Ramsey\Uuid\UuidInterface;

final readonly class UpdateCity
{
    public function __construct(
        private CityRepository $cityRepository,
    ) {}

    /**
     * @throws CityNotFoundException
     */
    public function update(UuidInterface $uuid, ?CityName $name, ?Geolocation $geolocation): City
    {
        $city = $this->cityRepository->findOrFail($uuid);

        if (!is_null($name)) {
            $city->name = $name;
        }

        if (!is_null($geolocation)) {
            $city->geolocation = $geolocation;
        }

        $this->cityRepository->save($city);

        return $city;
    }
}