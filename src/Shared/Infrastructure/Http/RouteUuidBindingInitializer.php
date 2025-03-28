<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Domain\UuidGenerator;
use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;
use Tempest\Container\Container;
use Tempest\Container\DynamicInitializer;
use Tempest\Reflection\ClassReflector;
use Tempest\Reflection\ParameterReflector;
use Tempest\Router\MatchedRoute;

final readonly class RouteUuidBindingInitializer implements DynamicInitializer
{
    public function canInitialize(ClassReflector $class): bool
    {
        return $class->getType()->matches(UuidInterface::class);
    }

    public function initialize(ClassReflector $class, Container $container): UuidInterface
    {
        $matchedRoute = $container->get(MatchedRoute::class);

        $matchedParameter = array_find(
            iterator_to_array($matchedRoute->route->handler->getParameters()),
            fn(ParameterReflector $parameter): bool => $parameter->getType()->equals($class->getType()),
        );

        if (is_null($matchedParameter)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid route binding, no found parameters matching class %s',
                    $class->getType()->getName(),
                ),
            );
        }

        return $container
            ->get(UuidGenerator::class)
            ->fromString($matchedRoute->params[$matchedParameter->getName()]);
    }
}