<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use Ramsey\Uuid\UuidInterface;

interface Entity
{
    public UuidInterface $uuid { get; }
}