<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Services;

final readonly class GeoapifyConfig
{
    public function __construct(
        public string $apiKey,
    ) {}
}