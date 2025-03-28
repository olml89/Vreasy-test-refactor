<?php

declare(strict_types=1);

namespace App\City\Domain;

use App\Shared\Domain\ValueObjectException;

final class InvalidGeolocationException extends ValueObjectException
{
    public function __construct(Geolocation $geolocation, CityName $cityName)
    {
        parent::__construct(
            sprintf(
                'Invalid %s for city %s',
                $geolocation,
                $cityName,
            ),
        );
    }
}