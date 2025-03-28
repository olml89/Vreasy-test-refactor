<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Services;

use App\Shared\Domain\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class RamseyUuidGenerator implements UuidGenerator
{
    public function random(): UuidInterface
    {
        return Uuid::uuid4();
    }

    public function fromString(string $uuid): UuidInterface
    {
        return Uuid::fromString($uuid);
    }
}