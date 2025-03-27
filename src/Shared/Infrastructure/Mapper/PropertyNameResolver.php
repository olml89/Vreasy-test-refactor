<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Mapper;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class PropertyNameResolver
{
    public function __construct(
        public string $original,
    ) {}
}