<?php

declare(strict_types=1);

namespace App\City\Domain;

use Ramsey\Uuid\UuidInterface;

interface CityRepository
{
    public function exists(CitySpecification $specification): bool;

    public function find(UuidInterface $uuid): ?City;

    /**
     * @throws CityNotFoundException
     */
    public function findOrFail(UuidInterface $uuid): City;

    /**
     * @return City[]
     */
    public function findBy(CitySpecification $specification): array;

    public function firstBy(CitySpecification $specification): ?City;

    public function remove(City $city): void;

    public function save(City $city): void;
}