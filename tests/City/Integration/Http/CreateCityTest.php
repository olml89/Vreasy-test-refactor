<?php

declare(strict_types=1);

namespace Tests\City\Integration\Http;

use App\City\Domain\CityRepository;
use App\City\Infrastructure\Database\InMemoryCityRepository;
use App\City\Infrastructure\Http\CreateCityController;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Uuid\Uuid;
use Tempest\Http\Status;
use Tests\Shared\Integration\Http\TestsApiEndpoint;
use Tests\Shared\Integration\IntegrationTestCase;

#[CoversMethod(CreateCityController::class, '__invoke')]
final class CreateCityTest extends IntegrationTestCase
{
    use TestsApiEndpoint;

    private readonly CityRepository $cityRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container->singleton(CityRepository::class, new InMemoryCityRepository());
        $this->cityRepository = $this->container->get(CityRepository::class);
    }

    private static function cityData(mixed $name = null, mixed $latitude = null, mixed $longitude = null): array
    {
        return [
            'name' => $name ?? 'Test City',
            'latitude' => $latitude ?? 2.09,
            'longitude' => $longitude ?? 100.13,
        ];
    }

    public static function provideInvalidCityData(): array
    {
        return [
            'name is missing' => [
                array_diff_key(
                    self::cityData(),
                    array_flip(['name']),
                ),
                'name',
                'Value must not be null'
            ],
            'name is not a string' => [
                self::cityData(name: 1),
                'name',
                'Value should be a string'
            ],
            'name too short' => [
                self::cityData(name: 'A'),
                'name',
                'Value should be between 2 and 100'
            ],
            'name too long' => [
                self::cityData(name: str_repeat('A', times: 101)),
                'name',
                'Value should be between 2 and 100'
            ],
            'latitude is missing' => [
                array_diff_key(
                    self::cityData(),
                    array_flip(['latitude']),
                ),
                'latitude',
                'Value must not be null'
            ],
            'latitude is not a float' => [
                self::cityData(latitude: 'A'),
                'latitude',
                'Value should be a float'
            ],
            'latitude is less than -90' => [
                self::cityData(latitude: -91),
                'latitude',
                'Value should be between -90 and 90'
            ],
            'latitude is more than -90' => [
                self::cityData(latitude: 91),
                'latitude',
                'Value should be between -90 and 90'
            ],
            'longitude is missing' => [
                array_diff_key(
                    self::cityData(),
                    array_flip(['longitude']),
                ),
                'longitude',
                'Value must not be null'
            ],
            'longitude is not a float' => [
                self::cityData(longitude: 'A'),
                'longitude',
                'Value should be a float'
            ],
            'longitude is less than -180' => [
                self::cityData(longitude: -181),
                'longitude',
                'Value should be between -180 and 180'
            ],
            'longitude is more than 180' => [
                self::cityData(longitude: 181),
                'longitude',
                'Value should be between -180 and 180'
            ],
        ];
    }

    #[DataProvider('provideInvalidCityData')]
    public function testItReturnsUnprocessableEntityIfCityCreationDataIsInvalid(
        array $invalidCityData,
        string $invalidField,
        string $errorMessage,
    ): void {
        $response = $this
            ->http
            ->post(
                uri: '/cities',
                body: $invalidCityData,
            );

        $this
            ->assertResponseError($response, Status::UNPROCESSABLE_CONTENT)
            ->assertFieldError($response, $invalidField, $errorMessage);
    }

    public function testItReturnsCreatedIfCityIsCreated(): void
    {
        $cityData = self::cityData();

        $response = $this
            ->http
            ->post(
                uri: '/cities',
                body: $cityData,
            );

        $this->assertNotNull(
            $this->cityRepository->find(Uuid::fromString($response->body['data']['uuid']))
        );

        $this->assertResponseData(
            response: $response,
            status: Status::CREATED,
            body: [
                'name' => $cityData['name'],
                'geolocation' => [
                    'latitude' => $cityData['latitude'],
                    'longitude' => $cityData['longitude'],
                ],
            ],
        );
    }
}