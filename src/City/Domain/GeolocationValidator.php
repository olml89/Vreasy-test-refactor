<?php

declare(strict_types=1);

namespace App\City\Domain;

interface GeolocationValidator
{
    /**
     * @throws InvalidGeolocationException
     */
    public function validate(Geolocation $geolocation, CityName $cityName);
}