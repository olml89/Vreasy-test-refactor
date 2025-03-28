<?php

declare(strict_types=1);

namespace Tests\City\Integration\Http;

use App\City\Domain\CityRepository;
use App\City\Infrastructure\Database\InMemoryCityRepository;
use App\City\Infrastructure\Http\RemoveCityController;
use PHPUnit\Framework\Attributes\CoversMethod;
use Ramsey\Uuid\Uuid;
use Tempest\Http\Status;
use Tests\City\CityFactory;
use Tests\Shared\Integration\Http\TestsApiEndpoint;
use Tests\Shared\Integration\IntegrationTestCase;

#[CoversMethod(RemoveCityController::class, '__invoke')]
final class RemoveCityTest extends IntegrationTestCase
{
    use TestsApiEndpoint;

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

    public function testItReturnsNoContentIfCityIsRemoved(): void
    {
        $city = CityFactory::create();
        $this->cityRepository->save($city);

        $response = $this
            ->http
            ->delete(
                uri: sprintf(
                    '/cities/%s',
                    $city->uuid,
                )
            );

        $this->assertNull(
            $this->cityRepository->find($city->uuid)
        );

        $this->assertResponseData(
            response: $response,
            status: Status::NO_CONTENT,
        );
    }
}