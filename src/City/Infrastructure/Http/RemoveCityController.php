<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Http;

use App\City\Application\RemoveCity;
use App\Shared\Infrastructure\Http\ResponseFactory;
use App\Shared\Infrastructure\Http\Responses\NoContent;
use Ramsey\Uuid\UuidInterface;
use Tempest\Router\Delete;

final readonly class RemoveCityController
{
    public function __construct(
        private RemoveCity $removeCity,
        private ResponseFactory $responseFactory,
    ) {}

    #[Delete(uri: '/cities/{uuid}')]
    public function __invoke(UuidInterface $uuid): NoContent
    {
        $this->removeCity->remove($uuid);

        return $this->responseFactory->noContent();
    }
}