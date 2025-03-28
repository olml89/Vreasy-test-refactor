<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Http;

use Tempest\Router\IsRequest;
use Tempest\Router\Request;
use Tempest\Validation\Rules\Between;
use Tempest\Validation\Rules\IsFloat;
use Tempest\Validation\Rules\IsString;
use Tempest\Validation\Rules\Length;

final class UpdateCityRequest implements Request
{
    use IsRequest;

    #[IsString]
    #[Length(min: 2, max: 100)]
    public ?string $name = null;

    #[IsFloat]
    #[Between(min: -90, max: 90)]
    public ?float $latitude = null;

    #[IsFloat]
    #[Between(min: -180, max: 180)]
    public ?float $longitude = null;
}