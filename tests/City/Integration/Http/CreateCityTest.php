<?php

declare(strict_types=1);

namespace Tests\City\Integration\Http;

use App\City\CreateCityController;
use PHPUnit\Framework\Attributes\CoversMethod;
use Tempest\Http\Status;
use Tests\Shared\Integration\Http\TestsApiEndpoint;
use Tests\Shared\Integration\IntegrationTestCase;

#[CoversMethod(CreateCityController::class, '__invoke')]
final class CreateCityTest extends IntegrationTestCase
{
    use TestsApiEndpoint;

    public function testItCreatesCity(): void
    {
        $cityData = [
            'name' => 'Test City',
            'latitude' => 2.09,
            'longitude' => 100.13,
        ];

        $response = $this
            ->http
            ->post(
                uri: '/cities',
                body: $cityData,
            );

        $this->assertResponse(
            expectedStatus: Status::CREATED,
            expectedBody: $cityData,
            response: $response,
        );
    }
}