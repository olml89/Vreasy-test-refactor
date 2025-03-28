<?php

declare(strict_types=1);

namespace App\City\Application;

use App\City\Domain\CityNotFoundException;
use App\City\Domain\CityRepository;
use App\Shared\Domain\UuidGenerator;

final readonly class RemoveCity
{
    public function __construct(
        private UuidGenerator $uuidGenerator,
        private CityRepository $repository,
    ) {}

    /**
     * @throws CityNotFoundException
     */
    public function remove(string $uuid): void
    {
        $uuid = $this->uuidGenerator->fromString($uuid);
        $city = $this->repository->findOrFail($uuid);

        $this->repository->remove($city);
    }
}