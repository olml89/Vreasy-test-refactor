<?php

declare(strict_types=1);

namespace App\City\Application;

use App\City\Domain\City;
use App\City\Domain\CityFactory;
use App\City\Domain\CityRepository;

final readonly class CreateCity
{
    public function __construct(
        private CityFactory $cityFactory,
        private CityRepository $cityRepository,
    ) {}

    public function create(string $name, float $latitude, float $longitude): City
    {
        $city = $this->cityFactory->create($name, $latitude, $longitude);
        $this->cityRepository->save($city);

        return $city;
    }
}