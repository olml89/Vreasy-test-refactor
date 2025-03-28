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
    protected function assertResponseData(TestResponseHelper $response, Status $status, ?array $body = null): static
    {
        $response->assertStatus($status);

        is_null($body)
            ? $this->assertNull($response->body)
            : $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
                expected: $body,
                actual: $response->body['data'],
                keysToBeConsidered: array_keys($body),
            );

        return $this;
    }

    protected function assertResponseError(TestResponseHelper $response, Status $status, ?string $message = null): static
    {
        $response->assertStatus($status);

        if (!is_null($message)) {
            $this->assertEquals($message, $response->body['message']);
        }

        return $this;
    }

    protected function assertFieldError(TestResponseHelper $response, string $field, string $error): static
    {
        $errorsForField = $response->body['errors'][$field] ?? [];
        $this->assertContains($error, $errorsForField);

        return $this;
    }
}