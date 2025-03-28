<?php

declare(strict_types=1);

namespace App\City\Domain;

use InvalidArgumentException;
use JsonSerializable;

final readonly class Geolocation implements JsonSerializable
{
    public function __construct(
        public float $latitude,
        public float $longitude,
    ) {
        if ($this->latitude < -90.0 || $this->latitude > 90.0) {
            throw new InvalidArgumentException('Latitude must be between -90.0 and -90.0');
        }

        if ($this->longitude < -180.0 || $this->longitude > 180.0) {
            throw new InvalidArgumentException('Longitude must be between -180.0 and 180.0');
        }
    }

    public static function from(?float $latitude, ?float $longitude): ?self
    {
        return is_null($latitude) || is_null($longitude) ? null : new self($latitude, $longitude);
    }

    public function equals(Geolocation $geolocation): bool
    {
        return $this->latitude === $geolocation->latitude && $this->longitude === $geolocation->longitude;
    }

    public function jsonSerialize(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}