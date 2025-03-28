<?php

declare(strict_types=1);

namespace Tests\City\Integration\Http;

use App\City\Domain\City;
use App\City\Domain\CityName;
use App\City\Domain\CityRepository;
use App\City\Domain\DuplicatedCityException;
use App\City\Domain\Geolocation;
use App\City\Domain\GeolocationValidator;
use App\City\Domain\InvalidGeolocationException;
use App\City\Infrastructure\Database\InMemoryCityRepository;
use App\City\Infrastructure\Http\UpdateCityController;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Uuid\Uuid;
use Tempest\Http\Status;
use Tests\City\CityFactory;
use Tests\City\Integration\FakeGeolocationValidator;
use Tests\Shared\Integration\Http\TestsApiEndpoint;
use Tests\Shared\Integration\IntegrationTestCase;

#[CoversMethod(UpdateCityController::class, '__invoke')]
final class UpdateCityTest extends IntegrationTestCase
{
    use TestsApiEndpoint;

    private readonly CityRepository $cityRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->container->singleton(CityRepository::class, new InMemoryCityRepository());
        $this->container->singleton(GeolocationValidator::class, new FakeGeolocationValidator());

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

    public function testItReturnsNotFoundIfCityDoesNotExist(): void
    {
        $uuid = Uuid::uuid4();

        $response = $this
            ->http
            ->patch(
                uri: sprintf(
                    '/cities/%s',
                    $uuid,
                ),
                body: self::cityData(),
            );

        $this->assertResponseError(
            response: $response,
            status: Status::NOT_FOUND,
            message: sprintf('City with uuid %s not found', $uuid)
        );
    }

    public static function provideInvalidCityData(): array
    {
        return [
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
        $city = CityFactory::create();
        $this->cityRepository->save($city);

        $response = $this
            ->http
            ->patch(
                uri: sprintf(
                    '/cities/%s',
                    $city->uuid,
                ),
                body: $invalidCityData,
            );

        $this
            ->assertResponseError($response, Status::UNPROCESSABLE_CONTENT)
            ->assertFieldError($response, $invalidField, $errorMessage);
    }

    public function testItReturnsBadRequestIfGeolocationValidatorFails(): void
    {
        $this->container->singleton(
            GeolocationValidator::class,
            new FakeGeolocationValidator(throwException: true)
        );

        $city = CityFactory::create();
        $this->cityRepository->save($city);
        $cityData = self::cityData();

        $response = $this
            ->http
            ->patch(
                uri: sprintf(
                    '/cities/%s',
                    $city->uuid,
                ),
                body: $cityData,
            );

        $this->assertResponseError(
            response: $response,
            status: Status::BAD_REQUEST,
            message: new InvalidGeolocationException(
                Geolocation::from($cityData['latitude'], $cityData['longitude']),
                CityName::from($cityData['name'])
            )->getMessage(),
        );
    }

    public function testItReturnsConflictIfCityUpdateDataIsDuplicated(): void
    {
        $city1 = CityFactory::create();
        $city2 = CityFactory::create(name: 'Different name', latitude: -1.03, longitude: 15.856);
        $this->cityRepository->save($city1);
        $this->cityRepository->save($city2);

        $response = $this
            ->http
            ->patch(
                uri: sprintf(
                    '/cities/%s',
                    $city2->uuid,
                ),
                body: [
                    'name' => (string)$city1->name,
                    'latitude' => $city1->geolocation->latitude,
                    'longitude' => $city1->geolocation->longitude,
                ],
            );

        $this->assertResponseError(
            response: $response,
            status: Status::CONFLICT,
            message: new DuplicatedCityException(
                $city1->name,
                $city1->geolocation,
            )->getMessage(),
        );
    }

    public static function provideValidCityData(): array
    {
        return [
            'unmodified data' => [
                CityFactory::create(),
                [],
            ],
            'original data' => [
                $city = CityFactory::create(),
                [
                    'name' => (string)$city->name,
                    'latitude' => $city->geolocation->latitude,
                    'longitude' => $city->geolocation->longitude,
                ],
            ],
            'modified data' => [
                CityFactory::create(),
                [
                    'name' => 'Different name',
                    'latitude' => -1.03,
                    'longitude' => 15.856,
                ],
            ],
        ];
    }

    #[DataProvider('provideValidCityData')]
    public function testItReturns200IfCityIsUpdated(City $city, array $updateCityData): void
    {
        $this->cityRepository->save($city);

        $expectedCityData = count($updateCityData) > 0
            ? $updateCityData
            : [
                'name' => (string)$city->name,
                'latitude' => $city->geolocation->latitude,
                'longitude' => $city->geolocation->longitude,
            ];

        $response = $this
            ->http
            ->patch(
                uri: sprintf(
                    '/cities/%s',
                    $city->uuid,
                ),
                body: $updateCityData,
            );

        $this->assertEquals($expectedCityData['name'], $city->name);
        $this->assertEquals($expectedCityData['latitude'], $city->geolocation->latitude);
        $this->assertEquals($expectedCityData['longitude'], $city->geolocation->longitude);

        $this->assertResponseData(
            response: $response,
            status: Status::OK,
            body: [
                'uuid' => (string) $city->uuid,
                'name' => $expectedCityData['name'],
                'geolocation' => [
                    'latitude' => $expectedCityData['latitude'],
                    'longitude' => $expectedCityData['longitude'],
                ],
            ]
        );
    }
}