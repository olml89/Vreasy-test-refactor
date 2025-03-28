<?php

declare(strict_types=1);

namespace App\City\Domain;

use App\Shared\Domain\DuplicatedEntityException;

final class DuplicatedCityException extends DuplicatedEntityException
{
    public function __construct(CityName $cityName, Geolocation $geolocation)
    {
        parent::__construct(
            message: sprintf(
                'City already exists with name %s or gelocation(latitude=%s, longitude=%s)',
                $cityName,
                $geolocation->latitude,
                $geolocation->longitude,
            ),
        );
    }
}