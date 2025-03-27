<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Database;

use App\Shared\Domain\Entity;
use Tempest\Database\Builder\TableName;
use Tempest\Database\Config\DatabaseConfig;
use Tempest\Database\DatabaseModel;
use Tempest\Database\IsDatabaseModel;

use function Tempest\get;

abstract class TempestModel implements DatabaseModel
{
    use IsDatabaseModel {
        table as protected originalTable;
    }

    protected const string ENTITY_CLASSNAME = Entity::class;

    public static function table(): TableName
    {
        return new TableName(
            get(DatabaseConfig::class)->namingStrategy->getName(static::ENTITY_CLASSNAME)
        );
    }
}