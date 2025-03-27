<?php

declare(strict_types=1);

namespace App\City;

use Tempest\Router\IsRequest;
use Tempest\Router\Request;
use Tempest\Validation\Rules\Between;
use Tempest\Validation\Rules\IsFloat;
use Tempest\Validation\Rules\IsString;
use Tempest\Validation\Rules\Length;
use Tempest\Validation\Rules\NotNull;

final class CreateCityRequest implements Request
{
    use IsRequest;

    #[NotNull]
    #[IsString]
    #[Length(min: 2, max: 100)]
    public string $name;

    #[NotNull]
    #[IsFloat]
    #[Between(min: -90, max: 90)]
    public float $latitude;

    #[NotNull]
    #[IsFloat]
    #[Between(min: -180, max: 180)]
    public float $longitude;
}