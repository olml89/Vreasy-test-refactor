<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Database;

use App\City\Domain\City;
use App\City\Domain\Geolocation;
use App\Shared\Infrastructure\Database\TempestModel;
use App\Shared\Infrastructure\Mapper\ValueObjectResolver;
use DateTimeImmutable;

final class CityModel extends TempestModel
{
    protected const string ENTITY_CLASSNAME = City::class;

    public function __construct(
        public string $uuid,
        public string $name,

        #[ValueObjectResolver(className: Geolocation::class, propertyName: 'latitude')]
        public float $latitude,

        #[ValueObjectResolver(className: Geolocation::class, propertyName: 'longitude')]
        public float $longitude,

        public DateTimeImmutable $created_at,
        public DateTimeImmutable $updated_at,
        public ?DateTimeImmutable $deleted_at,
    ) {}
}