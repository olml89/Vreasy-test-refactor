<?php

declare(strict_types=1);

namespace App\City\Domain;

use App\Shared\Domain\Entity;
use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

final class City implements Entity
{
    public function __construct(
        public readonly UuidInterface $uuid,
        public CityName $name,
        public Geolocation $geolocation,
        public DateTimeImmutable $createdAt = new DateTimeImmutable,
        public DateTimeImmutable $updatedAt = new DateTimeImmutable,
        public ?DateTimeImmutable $deletedAt = null,
    ) {}
}