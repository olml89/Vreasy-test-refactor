<?php

declare(strict_types=1);

namespace App\City\Domain;

use Ramsey\Uuid\UuidInterface;

interface CityRepository
{
    public function exists(CitySpecification $specification): bool;
    public function find(UuidInterface $uuid): ?City;
    /** @return City[] */
    public function findBy(CitySpecification $specification): array;
    public function findOneBy(CitySpecification $specification): ?City;
    public function save(City $city): void;
}