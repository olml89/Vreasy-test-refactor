<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper\Casters;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Tempest\Mapper\Caster;

final class UuidCaster implements Caster
{
    public function cast(mixed $input): UuidInterface
    {
        return Uuid::fromString($input);
    }
}