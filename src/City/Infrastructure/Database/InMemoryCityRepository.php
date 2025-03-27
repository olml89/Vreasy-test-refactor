<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Database;

use App\City\Domain\City;
use App\City\Domain\CityRepository;
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

    public function find(UuidInterface $uuid): ?City
    {
        return $this->cities[$uuid->toString()] ?? null;
    }

    public function save(City $city): void
    {
        $this->cities[$city->uuid->__toString()] = $city;
    }
}