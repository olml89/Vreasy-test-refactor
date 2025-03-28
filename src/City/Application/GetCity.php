<?php

declare(strict_types=1);

namespace App\City\Application;

use App\City\Domain\City;
use App\City\Domain\CityNotFoundException;
use App\City\Domain\CityRepository;
use App\Shared\Domain\UuidGenerator;

final readonly class GetCity
{
    public function __construct(
        private UuidGenerator $uuidGenerator,
        private CityRepository $repository,
    ) {}

    /**
     * @throws CityNotFoundException
     */
    public function get(string $uuid): City
    {
        $uuid = $this->uuidGenerator->fromString($uuid);

        return $this->repository->findOrFail($uuid);
    }
}