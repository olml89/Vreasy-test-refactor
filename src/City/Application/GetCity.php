<?php

declare(strict_types=1);

namespace App\City\Application;

use App\City\Domain\City;
use App\City\Domain\CityNotFoundException;
use App\City\Domain\CityRepository;
use Ramsey\Uuid\UuidInterface;

final readonly class GetCity
{
    public function __construct(
        private CityRepository $repository,
    ) {}

    /**
     * @throws CityNotFoundException
     */
    public function get(UuidInterface $uuid): City
    {
        return $this->repository->findOrFail($uuid);
    }
}