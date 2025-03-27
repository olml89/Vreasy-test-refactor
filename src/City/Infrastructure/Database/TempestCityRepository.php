<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Database;

use App\City\Domain\City;
use App\City\Domain\CityRepository;
use App\Shared\Infrastructure\Database\TempestRepository;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

final readonly class TempestCityRepository extends TempestRepository implements CityRepository
{
    protected function getModelClassName(): string
    {
        return CityModel::class;
    }

    public function find(UuidInterface $uuid): ?City
    {
        return null;
    }

    /**
     * @throws ReflectionException
     */
    public function save(City $city): void
    {
        $this->saveEntity($city);
    }
}