<?php

declare(strict_types=1);

namespace Tests\Shared\Integration;

use Dotenv\Dotenv;
use Tempest\Framework\Testing\IntegrationTest;

abstract class IntegrationTestCase extends IntegrationTest
{
    protected function setUp(): void
    {
        // Set up the root where to find composer.json and load the Kernel
        $this->root = dirname(__DIR__, 3);
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        restore_error_handler();
        restore_exception_handler();
    }
}