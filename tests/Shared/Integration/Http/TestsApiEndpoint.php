<?php

declare(strict_types=1);

namespace Tests\Shared\Integration\Http;

use Tempest\Framework\Testing\Http\TestResponseHelper;
use Tempest\Http\Status;
use Tests\Shared\Integration\IntegrationTestCase;

/**
 * @mixin IntegrationTestCase
 */
trait TestsApiEndpoint
{
    protected function assertResponse(Status $expectedStatus, array $expectedBody, TestResponseHelper $response): void
    {
        $response->assertStatus($expectedStatus);

        $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
            expected: $expectedBody,
            actual: $response->body,
            keysToBeConsidered: array_keys($expectedBody),
        );
    }
}