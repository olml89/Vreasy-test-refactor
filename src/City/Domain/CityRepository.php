<?php

declare(strict_types=1);

namespace App\City\Domain;

use Ramsey\Uuid\UuidInterface;

interface CityRepository
{
    public function find(UuidInterface $uuid): ?City;
    public function save(City $city): void;
}