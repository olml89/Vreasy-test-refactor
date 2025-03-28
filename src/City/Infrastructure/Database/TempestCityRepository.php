<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Database;

use App\City\Domain\City;
use App\City\Domain\CityRepository;
use App\City\Domain\CitySpecification;
use App\Shared\Infrastructure\Database\TempestRepository;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

final readonly class TempestCityRepository extends TempestRepository implements CityRepository
{
    protected function getModelClassName(): string
    {
        return CityModel::class;
    }

    /**
     * @throws ReflectionException
     */
    public function exists(CitySpecification $specification): bool
    {
        return $this->entityExists($specification);
    }

    public function find(UuidInterface $uuid): ?City
    {
        return null;
    }

    /**
     * @return City[]
     *
     * @throws ReflectionException
     */
    public function findBy(CitySpecification $specification): array
    {
        return $this->findEntitiesBy($specification);
    }

    /**
     * @throws ReflectionException
     */

    public function findOneBy(CitySpecification $specification): ?City
    {
        return $this->findOneEntityBy($specification);
    }

    /**
     * @throws ReflectionException
     */
    public function save(City $city): void
    {
        $this->saveEntity($city);
    }
}