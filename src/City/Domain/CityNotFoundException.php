<?php

declare(strict_types=1);

namespace App\City\Domain;

use App\Shared\Domain\EntityNotFoundException;
use Ramsey\Uuid\UuidInterface;

final class CityNotFoundException extends EntityNotFoundException
{
    public function __construct(UuidInterface $uuid)
    {
        parent::__construct(City::class, $uuid);
    }
}