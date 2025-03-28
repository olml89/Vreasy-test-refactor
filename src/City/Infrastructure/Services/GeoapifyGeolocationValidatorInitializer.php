<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Services;

use App\City\Domain\GeolocationValidator;
use Tempest\Container\Container;
use Tempest\Container\Initializer;
use Tempest\Container\Singleton;

final class GeoapifyGeolocationValidatorInitializer implements Initializer
{
    #[Singleton]
    public function initialize(Container $container): GeolocationValidator
    {
        return $container->get(GeoapifyGeolocationValidator::class);
    }
}