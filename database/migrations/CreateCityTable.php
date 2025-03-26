<?php

declare(strict_types=1);

/**
 * In order for TempestPHP to discover migration files, they must be PSR-4 autoloaded.
 * That means putting them in the src directory under the App namespace or creating a namespace for Migrations.
 */
namespace Database\Migrations;

use App\City\City;
use Tempest\Database\DatabaseMigration;
use Tempest\Database\QueryStatement;
use Tempest\Database\QueryStatements\CreateTableStatement;
use Tempest\Database\QueryStatements\DropTableStatement;

final class CreateCityTable implements DatabaseMigration
{
    private(set) string $name = '2025-03-25_16:25:00_create_city_table';

    public function up(): ?QueryStatement
    {
        return CreateTableStatement::forModel(City::class)
            ->primary()
            ->varchar('uuid', length: 36)->unique()
            ->varchar('name', length: 100)->unique()
            ->float('latitude')
            ->float('longitude')
            ->datetime('created_at')
            ->datetime('updated_at')
            ->datetime('deleted_at', nullable: true);
    }

    public function down(): ?QueryStatement
    {
        return DropTableStatement::forModel(City::class);
    }
}