<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Database;

use App\Shared\Domain\Entity;
use App\Shared\Infrastructure\Mapper\EntityToModelMapper;
use ReflectionException;
use Tempest\Database\Builder\TableName;
use Tempest\Database\DatabaseModel;

abstract readonly class TempestRepository
{
    public function __construct(
        protected EntityToModelMapper $entityToModelMapper,
    ) {}

    /**
     * @return class-string<DatabaseModel>
     */
    abstract protected function getModelClassName(): string;

    public function tableName(): TableName
    {
        return static::getModelClassName()::table();
    }

    /**
     * @throws ReflectionException
     */
    protected function saveEntity(Entity $entity): void
    {
        $model = $this->entityToModelMapper->map($entity, static::getModelClassName());
        $model->save();
    }
}