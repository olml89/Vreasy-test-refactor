<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http;

use App\Shared\Domain\Entity;
use App\Shared\Domain\DuplicatedEntityException;
use App\Shared\Domain\EntityNotFoundException;
use App\Shared\Infrastructure\Http\Responses\Conflict;
use App\Shared\Infrastructure\Http\Responses\Created;
use App\Shared\Infrastructure\Http\Responses\NoContent;
use App\Shared\Infrastructure\Http\Responses\NotFound;
use App\Shared\Infrastructure\Http\Responses\Ok;
use App\Shared\Infrastructure\Http\Responses\ServerError;
use App\Shared\Infrastructure\Http\Responses\UnprocessableEntity;
use App\Shared\Infrastructure\PresenterFactory;
use Tempest\Core\AppConfig;
use Tempest\Router\Response;
use Tempest\Validation\Exceptions\ValidationException;
use Throwable;

final readonly class ResponseFactory
{
    public function __construct(
        private PresenterFactory $presenterFactory,
        private AppConfig $appConfig,
    ) {}

    public function ok(Entity $entity): Ok
    {
        return new Ok($this->presenterFactory->present($entity));
    }

    public function created(Entity $entity): Created
    {
        return new Created($this->presenterFactory->present($entity));
    }

    public function noContent(): NoContent
    {
        return new NoContent();
    }

    public function notFound(EntityNotFoundException $exception): NotFound
    {
        return new NotFound($exception, $this->shouldDebugException());
    }

    public function conflict(DuplicatedEntityException $exception): Conflict
    {
        return new Conflict($exception, $this->shouldDebugException());
    }

    public function unprocessableEntity(ValidationException $exception): UnprocessableEntity
    {
        return new UnprocessableEntity($exception);
    }

    public function serverError(Throwable $throwable): ServerError
    {
        return new ServerError($throwable, $this->shouldDebugException());
    }

    public function mapToResponse(Throwable $throwable): Response
    {
        return match (true) {
            $throwable instanceof EntityNotFoundException => $this->notFound($throwable),
            $throwable instanceof ValidationException => $this->unprocessableEntity($throwable),
            $throwable instanceof DuplicatedEntityException => $this->conflict($throwable),
            default => $this->serverError($throwable),
        };
    }

    private function shouldDebugException(): bool
    {
        return !$this->appConfig->environment->isProduction();
    }
}