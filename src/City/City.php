<?php

declare(strict_types=1);

namespace App\City;

use DateTimeImmutable;
use Tempest\Database\DatabaseModel;
use Tempest\Database\IsDatabaseModel;

final class City implements DatabaseModel
{
    use IsDatabaseModel;

    public function __construct(
        public string $uuid,
        public string $name,
        public float $latitude,
        public float $longitude,
        public DateTimeImmutable $created_at = new DateTimeImmutable,
        public DateTimeImmutable $updated_at = new DateTimeImmutable,
        public ?DateTimeImmutable $deleted_at = null,
    ) {}
}