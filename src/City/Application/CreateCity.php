<?php

declare(strict_types=1);

namespace App\City\Application;

use App\City\Domain\City;
use App\City\Domain\DuplicatedCityException;
use App\City\Domain\CityFactory;
use App\City\Domain\CityName;
use App\City\Domain\DuplicatedNewCitySpecification;
use App\City\Domain\CityRepository;
use App\City\Domain\Geolocation;
use App\City\Domain\GeolocationValidator;
use App\City\Domain\InvalidGeolocationException;

final readonly class CreateCity
{
    public function __construct(
        private GeolocationValidator $geolocationValidator,
        private CityFactory $cityFactory,
        private CityRepository $cityRepository,
    ) {}

    /**
     * @throws InvalidGeolocationException
     * @throws DuplicatedCityException
     */
    public function create(CityName $name, Geolocation $geolocation): City
    {
        $this->geolocationValidator->validate($geolocation, $name);

        if ($this->cityRepository->exists(new DuplicatedNewCitySpecification($name, $geolocation))) {
            throw new DuplicatedCityException($name, $geolocation);
        }

        $city = $this->cityFactory->create($name, $geolocation);
        $this->cityRepository->save($city);

        return $city;
    }
}