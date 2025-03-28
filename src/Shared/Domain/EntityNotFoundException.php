<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use Ramsey\Uuid\UuidInterface;
use RuntimeException;

abstract class EntityNotFoundException extends RuntimeException
{
    public function __construct(string $entityClass, UuidInterface $uuid)
    {
        parent::__construct(
            message: sprintf(
                '%s with uuid %s not found',
                basename(str_replace('\\', '/', $entityClass)),
                $uuid->toString(),
            ),
        );
    }
}