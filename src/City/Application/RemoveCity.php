<?php

declare(strict_types=1);

namespace App\City\Application;

use App\City\Domain\CityNotFoundException;
use App\City\Domain\CityRepository;
use Ramsey\Uuid\UuidInterface;

final readonly class RemoveCity
{
    public function __construct(
        private CityRepository $repository,
    ) {}

    /**
     * @throws CityNotFoundException
     */
    public function remove(UuidInterface $uuid): void
    {
        $city = $this->repository->findOrFail($uuid);

        $this->repository->remove($city);
    }
}