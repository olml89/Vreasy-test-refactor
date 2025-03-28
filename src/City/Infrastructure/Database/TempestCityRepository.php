<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Database;

use App\City\Domain\City;
use App\City\Domain\CityNotFoundException;
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
        return !is_null($this->firstBy($specification));
    }

    /**
     * @throws ReflectionException
     */
    public function find(UuidInterface $uuid): ?City
    {
        return $this->findEntity($uuid);
    }

    /**
     * @throws CityNotFoundException
     * @throws ReflectionException
     */
    public function findOrFail(UuidInterface $uuid): City
    {
        return $this->find($uuid) ?? throw new CityNotFoundException($uuid);
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

    public function firstBy(CitySpecification $specification): ?City
    {
        return $this->findEntitiesBy($specification)[0] ?? null;
    }

    public function remove(City $city): void
    {
        $this->removeEntity($city);
    }

    /**
     * @throws ReflectionException
     */
    public function save(City $city): void
    {
        $this->saveEntity($city);
    }
}