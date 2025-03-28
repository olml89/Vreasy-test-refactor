<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Http;

use App\City\Application\GetCity;
use App\Shared\Infrastructure\Http\ResponseFactory;
use App\Shared\Infrastructure\Http\Responses\Ok;
use Ramsey\Uuid\UuidInterface;
use Tempest\Router\Get;

final readonly class GetCityController
{
    public function __construct(
        private GetCity $getCity,
        private ResponseFactory $responseFactory,
    ) {}

    #[Get(uri: '/cities/{uuid}')]
    public function __invoke(UuidInterface $uuid): Ok
    {
        $city = $this->getCity->get($uuid);

        return $this->responseFactory->ok($city);
    }
}