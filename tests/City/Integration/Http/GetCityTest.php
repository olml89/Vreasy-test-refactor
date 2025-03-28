<?php

declare(strict_types=1);

namespace Tests\City\Integration\Http;

use App\City\Domain\CityRepository;
use App\City\Infrastructure\Database\InMemoryCityRepository;
use App\City\Infrastructure\Http\GetCityController;
use PHPUnit\Framework\Attributes\CoversMethod;
use Ramsey\Uuid\Uuid;
use Tempest\Http\Status;
use Tests\City\CityFactory;
use Tests\Shared\Integration\Http\TestsApiEndpoint;
use Tests\Shared\Integration\IntegrationTestCase;

#[CoversMethod(GetCityController::class, '__invoke')]
final class GetCityTest extends IntegrationTestCase
{
    use TestsApiEndpoint;

    private readonly CityRepository $cityRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container->singleton(CityRepository::class, new InMemoryCityRepository());
        $this->cityRepository = $this->container->get(CityRepository::class);
    }

    public function testItReturnsNotFoundIfCityDoesNotExist(): void
    {
        $uuid = Uuid::uuid4();

        $response = $this
            ->http
            ->get(
                uri: sprintf(
                    '/cities/%s',
                    $uuid,
                )
            );

        $this->assertResponseError(
            response: $response,
            status: Status::NOT_FOUND,
            message: sprintf('City with uuid %s not found', $uuid)
        );
    }

    public function testItReturns200IfCityIsFound(): void
    {
        $city = CityFactory::create();
        $this->cityRepository->save($city);

        $response = $this
            ->http
            ->get(
                uri: sprintf(
                    '/cities/%s',
                    $city->uuid,
                )
            );

        $this->assertResponseData(
            response: $response,
            status: Status::OK,
            body: [
                'uuid' => (string) $city->uuid,
                'name' => (string) $city->name,
                'geolocation' => [
                    'latitude' => $city->geolocation->latitude,
                    'longitude' => $city->geolocation->longitude,
                ],
            ]
        );
    }
}