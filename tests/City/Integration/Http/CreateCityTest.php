<?php

declare(strict_types=1);

namespace Tests\City\Integration\Http;

use App\City\Domain\CityName;
use App\City\Domain\CityRepository;
use App\City\Domain\DuplicatedCityException;
use App\City\Domain\Geolocation;
use App\City\Domain\GeolocationValidator;
use App\City\Domain\InvalidGeolocationException;
use App\City\Infrastructure\Database\InMemoryCityRepository;
use App\City\Infrastructure\Http\CreateCityController;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use Ramsey\Uuid\Uuid;
use Tempest\Http\Status;
use Tests\City\CityFactory;
use Tests\City\Integration\FakeGeolocationValidator;
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

    public function testItReturnsBadRequestIfGeolocationValidatorFails(): void
    {
        $this->container->singleton(
            GeolocationValidator::class,
            new FakeGeolocationValidator(throwException: true)
        );

        $cityData = self::cityData();

        $response = $this
            ->http
            ->post(
                uri: '/cities',
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

    public static function provideAlreadyExistingCityData(): array
    {
        return [
            'already existing name' => [
                self::cityData(latitude: 10.0, longitude: 20.0),
            ],
            'already existing geolocation' => [
                self::cityData(name: 'Different name'),
            ],
        ];
    }

    #[DataProvider('provideAlreadyExistingCityData')]
    public function testItReturnsConflictIfCityAlreadyExists(array $alreadyExistingCityData): void
    {
        $cityData = self::cityData();
        $city = CityFactory::create($cityData['name'], $cityData['latitude'], $cityData['longitude']);
        $this->cityRepository->save($city);

        $response = $this
            ->http
            ->post(
                uri: '/cities',
                body: $alreadyExistingCityData,
            );

        $this->assertResponseError(
            response: $response,
            status: Status::CONFLICT,
            message: new DuplicatedCityException(
                CityName::from($alreadyExistingCityData['name']),
                Geolocation::from($alreadyExistingCityData['latitude'], $alreadyExistingCityData['longitude']),
            )->getMessage(),
        );
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