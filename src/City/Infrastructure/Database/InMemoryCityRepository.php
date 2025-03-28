<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Database;

use App\City\Domain\City;
use App\City\Domain\CityNotFoundException;
use App\City\Domain\CityRepository;
use App\City\Domain\CitySpecification;
use Ramsey\Uuid\UuidInterface;

final class InMemoryCityRepository implements CityRepository
{
    /**
     * @var array<string, City>
     */
    private array $cities;

    public function __construct(City ...$cities)
    {
        $this->cities = $cities;
    }

    public function exists(CitySpecification $specification): bool
    {
        return !is_null($this->firstBy($specification));
    }

    public function find(UuidInterface $uuid): ?City
    {
        return $this->cities[$uuid->toString()] ?? null;
    }

    /**
     * @throws CityNotFoundException
     */
    public function findOrFail(UuidInterface $uuid): City
    {
        return $this->find($uuid) ?? throw new CityNotFoundException($uuid);
    }

    /**
     * @return City[]
     */
    public function findBy(CitySpecification $specification): array
    {
        return array_filter($this->cities, fn(City $city): bool => $specification->isSatisfiedBy($city));
    }

    public function firstBy(CitySpecification $specification): ?City
    {
        return array_find($this->cities, fn(City $city): bool => $specification->isSatisfiedBy($city));
    }

    public function save(City $city): void
    {
        $this->cities[$city->uuid->__toString()] = $city;
    }
}