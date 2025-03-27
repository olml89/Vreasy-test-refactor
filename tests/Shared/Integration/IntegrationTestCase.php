<?php

declare(strict_types=1);

namespace Tests\Shared\Integration;

use App\Shared\EnvironmentLoader;
use Tempest\Core\Environment;
use Tempest\Database\Transactions\TransactionManager;
use Tempest\Framework\Testing\IntegrationTest;

abstract class IntegrationTestCase extends IntegrationTest
{
    protected function setUp(): void
    {
        // Set up the root where to find composer.json and load the Kernel
        $this->root = dirname(__DIR__, 3);
        parent::setUp();

        // Overwrite needed environment variables
        $this->container->get(EnvironmentLoader::class)->load(Environment::TESTING);

        // Prepare the database to run each test on atomic transactions
        $this->container->get(TransactionManager::class)->begin();
    }

    protected function tearDown(): void
    {
        // Rollback the transactions done on the test to clear the database
        $this->container->get(TransactionManager::class)->rollback();

        parent::tearDown();

        restore_error_handler();
        restore_exception_handler();
    }
}