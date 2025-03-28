<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Http;

use App\City\Application\CreateCity;
use App\Shared\Infrastructure\Http\ResponseFactory;
use App\Shared\Infrastructure\Http\Responses\Created;
use Tempest\Router\Post;
use Tempest\Router\Response;

final readonly class CreateCityController
{
    public function __construct(
        private CreateCity $createCity,
        private ResponseFactory $responseFactory,
    ) {}

    #[Post('/cities')]
    public function __invoke(CreateCityRequest $request): Created
    {
        $city = $this->createCity->create(
            name: $request->name,
            latitude: $request->latitude,
            longitude: $request->longitude,
        );

        return $this->responseFactory->created($city);
    }
}