<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Database;

use App\City\Domain\CityRepository;
use Tempest\Container\Container;
use Tempest\Container\Initializer;

final readonly class CityRepositoryInitializer implements Initializer
{
    public function initialize(Container $container): CityRepository
    {
        return $container->get(TempestCityRepository::class);
    }
}