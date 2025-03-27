<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use Tempest\Container\Container;
use Tempest\Container\Initializer;
use Tempest\Container\Singleton;
use Tempest\Router\Router;

final readonly class ApiRouterInitializer implements Initializer
{
    #[Singleton]
    public function initialize(Container $container): Router
    {
        return $container->get(ApiRouter::class);
    }
}