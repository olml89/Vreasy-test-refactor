<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure;

use Dotenv\Dotenv;
use Tempest\Core\Environment;
use Tempest\Core\Kernel;

final readonly class EnvironmentLoader
{
    public function __construct(
        private Kernel $kernel,
    ) {}

    public function load(Environment $env): void
    {
        $envDir = dirname(__DIR__, 2);

        $envFileName = match ($env) {
            Environment::LOCAL => '.env',
            default => '.env.' . $env->value,
        };

        if (file_exists(sprintf('%s/%s', $envDir, $envFileName))) {
            $this->loadEnvironment($envDir, $envFileName);
            $this->loadConfig();
        }
    }

    private function loadEnvironment(string $envDir, string $envFileName): void
    {
        // Create it mutable to overwrite existing environment variables.
        $dotenv = Dotenv::createUnsafeMutable($envDir, $envFileName);
        $dotenv->safeLoad();
    }

    private function loadConfig(): void
    {
        $this->kernel->loadConfig()->loadDiscovery();
    }
}