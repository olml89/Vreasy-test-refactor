<?php

declare(strict_types=1);

namespace App\City\Application;

use App\City\Domain\City;
use App\City\Domain\CityName;
use App\City\Domain\CityNotFoundException;
use App\City\Domain\CityRepository;
use App\City\Domain\DuplicatedCityException;
use App\City\Domain\DuplicatedUpdatedCitySpecification;
use App\City\Domain\Geolocation;
use App\City\Domain\GeolocationValidator;
use App\City\Domain\InvalidGeolocationException;
use Ramsey\Uuid\UuidInterface;

final readonly class UpdateCity
{
    public function __construct(
        private CityRepository $cityRepository,
        private GeolocationValidator $geolocationValidator,
    ) {}

    /**
     * @throws CityNotFoundException
     * @throws InvalidGeolocationException
     * @throws DuplicatedCityException
     */
    public function update(UuidInterface $uuid, ?CityName $name, ?Geolocation $geolocation): City
    {
        $city = $this->cityRepository->findOrFail($uuid);

        if (!is_null($name)) {
            $city->name = $name;
        }

        if (!is_null($geolocation)) {
            $this->geolocationValidator->validate($geolocation, $city->name);
            $city->geolocation = $geolocation;
        }

        /**
         * We only have to show the city as duplicated if the values match with a different and already
         * existing city
         */
        if ($this->cityRepository->exists(new DuplicatedUpdatedCitySpecification($city))) {
            throw new DuplicatedCityException($city->name, $city->geolocation);
        }

        $this->cityRepository->save($city);

        return $city;
    }
}