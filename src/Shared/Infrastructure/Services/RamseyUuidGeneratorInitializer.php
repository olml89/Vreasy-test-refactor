<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Services;

use App\Shared\Domain\UuidGenerator;
use Tempest\Container\Container;
use Tempest\Container\Initializer;
use Tempest\Container\Singleton;

final class RamseyUuidGeneratorInitializer implements Initializer
{
    #[Singleton]
    public function initialize(Container $container): UuidGenerator
    {
        return $container->get(RamseyUuidGenerator::class);
    }
}