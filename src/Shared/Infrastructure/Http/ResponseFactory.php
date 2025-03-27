<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Domain\Entity;
use App\Shared\Infrastructure\Http\Responses\Created;
use App\Shared\Infrastructure\Http\Responses\ServerError;
use App\Shared\Infrastructure\Http\Responses\UnprocessableEntity;
use App\Shared\Infrastructure\PresenterFactory;
use Tempest\Core\AppConfig;
use Tempest\Router\Exceptions\NotFoundException;
use Tempest\Router\Response;
use Tempest\Router\Responses\NotFound;
use Tempest\Validation\Exceptions\ValidationException;
use Throwable;

final readonly class ResponseFactory
{
    public function __construct(
        private PresenterFactory $presenterFactory,
        private AppConfig $appConfig,
    ) {}

    public function created(Entity $entity): Created
    {
        return new Created($this->presenterFactory->present($entity));
    }

    public function unprocessableEntity(ValidationException $validationException): UnprocessableEntity
    {
        return new UnprocessableEntity($validationException);
    }

    public function serverError(Throwable $throwable): ServerError
    {
        return new ServerError($throwable, $this->shouldDebugException());
    }

    public function mapToResponse(Throwable $throwable): Response
    {
        return match (true) {
            $throwable instanceof NotFoundException => new NotFound(),
            $throwable instanceof ValidationException => $this->unprocessableEntity($throwable),
            default => $this->serverError($throwable),
        };
    }

    private function shouldDebugException(): bool
    {
        return !$this->appConfig->environment->isProduction();
    }
}