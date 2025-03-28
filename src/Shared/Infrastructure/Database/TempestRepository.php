<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Database;

use App\Shared\Domain\Criteria\Specification;
use App\Shared\Domain\Entity;
use App\Shared\Infrastructure\Mapper\EntityToModelMapper;
use App\Shared\Infrastructure\Mapper\ModelToEntityMapper;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;
use Tempest\Database\Builder\ModelDefinition;
use Tempest\Database\Builder\ModelQueryBuilder;
use Tempest\Database\Builder\TableName;

abstract readonly class TempestRepository
{
    public function __construct(
        protected EntityToModelMapper $entityToModelMapper,
        protected ModelToEntityMapper $modelToEntityMapper,
        protected CriteriaToModelQueryConverter $criteriaToModelQueryConverter,
    ) {
    }

    /**
     * @return class-string<TempestModel>
     */
    abstract protected function getModelClassName(): string;

    /**
     * @throws ReflectionException
     */
    protected function mapModelToEntity(TempestModel $model): Entity
    {
        return $this->modelToEntityMapper->map($model, $this->getModelClassName()::getEntityClassName());
    }

    public function tableName(): TableName
    {
        return $this->getModelClassName()::table();
    }

    protected function query(): ModelQueryBuilder
    {
        return $this->getModelClassName()::query();
    }

    private function findModel(UuidInterface $uuid): ?TempestModel
    {
        return $this
            ->query()
            ->whereField('uuid', (string)$uuid)
            ->limit(1)
            ->first();
    }

    /**
     * @throws ReflectionException
     */
    protected function findEntity(UuidInterface $uuid): ?Entity
    {
        return $this
            ->modelToEntityMapper
            ->map(
                $this->findModel($uuid),
                $this->getModelClassName()::getEntityClassName()
            );
    }

    /**
     * @return Entity[]
     *
     * @throws ReflectionException
     */
    protected function findEntitiesBy(Specification $specification): array
    {
        $modelQueryBuilderBinder = new ModelQueryBuilderBinder(
            new ModelDefinition($this->getModelClassName()),
            $this->query()
        );

        $records = $this
            ->criteriaToModelQueryConverter
            ->convertCriteriaToQueryBuilder($specification->criteria(), $modelQueryBuilderBinder)
            ->all();

        return array_map(
            fn(TempestModel $model): Entity => $this
                ->modelToEntityMapper
                ->map(
                    $model,
                    $this->getModelClassName()::getEntityClassName()
                ),
            $records
        );
    }

    public function removeEntity(Entity $entity): void
    {
        $this->findModel($entity->uuid)?->delete();
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