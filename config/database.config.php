<?php

declare(strict_types=1);

/**
 * In order for TempestPHP to discover config files, they must be PSR-4 autoloaded.
 * That means putting them in the src directory under the App namespace or creating a namespace for Config.
 */
namespace Config;

use Tempest\Database\Config\MysqlConfig;

use function Tempest\env;

return new MysqlConfig(
    host: env('DB_HOST'),
    port: env('DB_PORT'),
    username: env('DB_USER'),
    password: env('DB_PASSWORD'),
    database: env('DB_NAME'),
);