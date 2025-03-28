<?php

declare(strict_types=1);

namespace App\City\Application;

use App\City\Domain\City;
use App\City\Domain\DuplicatedCityException;
use App\City\Domain\CityFactory;
use App\City\Domain\CityName;
use App\City\Domain\DuplicatedCitySpecification;
use App\City\Domain\CityRepository;
use App\City\Domain\Geolocation;

final readonly class CreateCity
{
    public function __construct(
        private CityFactory $cityFactory,
        private CityRepository $cityRepository,
    ) {}

    public function create(string $name, float $latitude, float $longitude): City
    {
        $cityName = new CityName($name);
        $geolocation = new Geolocation($latitude, $longitude);

        if ($this->cityRepository->exists(new DuplicatedCitySpecification($cityName, $geolocation))) {
            throw new DuplicatedCityException($cityName, $geolocation);
        }

        $city = $this->cityFactory->create($cityName, $geolocation);
        $this->cityRepository->save($city);

        return $city;
    }
}