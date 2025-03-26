<?php

declare(strict_types=1);

namespace App\City;

use Tempest\Router\IsRequest;
use Tempest\Router\Request;
use Tempest\Validation\Rules\Between;
use Tempest\Validation\Rules\IsFloat;
use Tempest\Validation\Rules\Length;

final class CreateCityRequest implements Request
{
    use IsRequest;

    #[Length(min: 2, max: 100)]
    public string $name;

    #[IsFloat]
    #[Between(min: -90, max: 90)]
    public float $latitude;

    #[IsFloat]
    #[Between(min: -180, max: 180)]
    public float $longitude;
}