<?php

declare(strict_types=1);

namespace App\City\Infrastructure\Http;

use App\City\Application\UpdateCity;
use App\City\Domain\CityName;
use App\City\Domain\Geolocation;
use App\Shared\Infrastructure\Http\ResponseFactory;
use App\Shared\Infrastructure\Http\Responses\Ok;
use Ramsey\Uuid\UuidInterface;
use Tempest\Router\Patch;

final readonly class UpdateCityController
{
    public function __construct(
        private UpdateCity $updateCity,
        private ResponseFactory $responseFactory,
    ) {}

    #[Patch('/cities/{uuid}')]
    public function __invoke(UuidInterface $uuid, UpdateCityRequest $request): Ok
    {
        $city = $this->updateCity->update(
            uuid: $uuid,
            name: CityName::from($request->name),
            geolocation: Geolocation::from(latitude: $request->latitude, longitude: $request->longitude),
        );

        return $this->responseFactory->ok($city);
    }
}