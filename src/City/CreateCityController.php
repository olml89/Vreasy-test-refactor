<?php

declare(strict_types=1);

namespace App\City;

use Ramsey\Uuid\Uuid;
use Tempest\Router\Post;
use Tempest\Router\Response;

final readonly class CreateCityController
{
    public function __construct(
        private ResponseFactory $responseFactory,
    ) {}

    #[Post('/cities')]
    public function __invoke(CreateCityRequest $request): Response
    {
        $city = new City(
            uuid: Uuid::uuid4()->toString(),
            name: $request->name,
            latitude: $request->latitude,
            longitude: $request->longitude,
        );

        $city->save();

        return $this->responseFactory->created($city);
    }
}