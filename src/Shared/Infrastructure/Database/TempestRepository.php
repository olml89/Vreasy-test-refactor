<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Database;

use App\Shared\Domain\Criteria\Specification;
use App\Shared\Domain\Entity;
use App\Shared\Infrastructure\Mapper\EntityToModelMapper;
use App\Shared\Infrastructure\Mapper\ModelToEntityMapper;
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

    /**
     * @throws ReflectionException
     */
    protected function entityExists(Specification $specification): bool
    {
        return !is_null($this->findOneBy($specification));
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
            fn(TempestModel $model): Entity => $this->mapModelToEntity($model),
            $records
        );
    }

    /**
     * @throws ReflectionException
     */
    public function findOneEntityBy(Specification $specification): ?Entity
    {
        return $this->findEntitiesBy($specification)[0] ?? null;
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