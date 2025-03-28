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

    public function create(CityName $name, Geolocation $geolocation): City
    {
        if ($this->cityRepository->exists(new DuplicatedCitySpecification($name, $geolocation))) {
            throw new DuplicatedCityException($name, $geolocation);
        }

        $city = $this->cityFactory->create($name, $geolocation);
        $this->cityRepository->save($city);

        return $city;
    }
}