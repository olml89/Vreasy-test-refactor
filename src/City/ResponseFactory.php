<?php

declare(strict_types=1);

namespace App\City;

use App\Shared\Http\Responses\Created;
use App\Shared\Http\Responses\ServerError;
use App\Shared\Http\Responses\UnprocessableEntity;
use Tempest\Core\AppConfig;
use Tempest\Database\DatabaseModel;
use Tempest\Mapper\ObjectFactory;
use Tempest\Router\Exceptions\NotFoundException;
use Tempest\Router\Response;
use Tempest\Router\Responses\NotFound;
use Tempest\Validation\Exceptions\ValidationException;
use Throwable;

final readonly class ResponseFactory
{
    public function __construct(
        private ObjectFactory $objectFactory,
        private AppConfig $appConfig,
    ) {}

    private function present(DatabaseModel $model): array
    {
        return $this->objectFactory->with(ModelToPresenterMapper::class)->map($model, 'array');
    }

    public function created(DatabaseModel $model): Created
    {
        return new Created($this->present($model));
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