<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use Psr\Http\Message\ServerRequestInterface as PsrRequest;
use Tempest\Router\Exceptions\InvalidRouteException;
use Tempest\Router\GenericRouter;
use Tempest\Router\Request;
use Tempest\Router\Response;
use Tempest\Router\Router;
use Throwable;

final readonly class ApiRouter implements Router
{
    public function __construct(
        private GenericRouter $router,
        private ResponseFactory $responseFactory,
    ) {
        $this->router->throwExceptions();
    }

    public function dispatch(PsrRequest|Request $request): Response
    {
        try {
            return $this->router->dispatch($request);
        }
        catch (Throwable $e) {
            return $this->responseFactory->mapToResponse($e);
        }
    }

    /**
     * @throws InvalidRouteException
     */
    public function toUri(array|string $action, ...$params): string
    {
        return $this->router->toUri($action, $params);
    }

    public function addMiddleware(string $middlewareClass): void
    {
        $this->router->addMiddleware($middlewareClass);
    }
}