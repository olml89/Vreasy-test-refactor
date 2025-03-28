<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Database;

use App\Shared\Domain\Criteria\CompositeExpression\AndExpression;
use App\Shared\Domain\Criteria\CompositeExpression\CompositeExpression;
use App\Shared\Domain\Criteria\CompositeExpression\NotExpression;
use App\Shared\Domain\Criteria\CompositeExpression\OrExpression;
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Expression;
use App\Shared\Domain\Criteria\Filter\Filter;
use App\Shared\Domain\Criteria\Order\Order;
use Tempest\Database\Builder\ModelQueryBuilder;

final readonly class CriteriaToModelQueryConverter
{
    public function convertCriteriaToQueryBuilder(
        Criteria $criteria,
        ModelQueryBuilderBinder $queryBuilderBinder,
    ): ModelQueryBuilder {
        $this->convertExpression($queryBuilderBinder, $criteria->expression);
        $this->convertOrder($queryBuilderBinder, $criteria->order);
        $this->convertLimitAndOffset($queryBuilderBinder, $criteria->limit, $criteria->offset);

        return $queryBuilderBinder->query();
    }

    private function convertExpression(ModelQueryBuilderBinder $queryBuilderBinder, Expression $expression): void
    {
        $queryBuilderBinder
            ->query()
            ->where(
                $this->convertExpressionToString($queryBuilderBinder, $expression)
            );
    }

    private function convertOrder(ModelQueryBuilderBinder $queryBuilderBinder, ?Order $order): void
    {
        if (is_null($order)) {
            return;
        }

        $queryBuilderBinder
            ->query()
            ->orderBy(
                sprintf('%s %s', $order->orderBy, $order->orderType->value),
            );
    }

    private function convertLimitAndOffset(ModelQueryBuilderBinder $queryBuilderBinder, ?int $limit, ?int $offset): void
    {
        if (!is_null($limit)) {
            $queryBuilderBinder->query()->limit($limit);
        }

        if (!is_null($offset)) {
            $queryBuilderBinder->query()->offset($offset);
        }
    }

    private function convertExpressionToString(ModelQueryBuilderBinder $queryBuilderBinder, Expression $expression): string
    {
        if ($expression instanceof CompositeExpression) {
            return $this->convertCompositeExpressionToString($queryBuilderBinder, $expression);
        }

        /** @var Filter $expression */
        return $this->convertSimpleExpressionToString($queryBuilderBinder, $expression);
    }

    private function convertSimpleExpressionToString(ModelQueryBuilderBinder $queryBuilderBinder, Filter $expression): string
    {
        return $queryBuilderBinder->bindExpression($expression);
    }

    private function convertCompositeExpressionToString(ModelQueryBuilderBinder $queryBuilderBinder, CompositeExpression $expression): string
    {
        if ($expression instanceof NotExpression) {
            return sprintf(
                '%s (%s)',
                $expression->type->value,
                $this->convertClauseToString($queryBuilderBinder, $expression->clause)
            );
        }

        /** @var AndExpression|OrExpression $expression */
        return $this->convertMultiClauseExpressionToString($queryBuilderBinder, $expression);
    }

    private function convertMultiClauseExpressionToString(
        ModelQueryBuilderBinder $queryBuilderBinder,
        AndExpression|OrExpression $expression,
    ): string {
        return implode(
            sprintf(
                ' %s ',
                $expression->type->value,
            ),
            array_map(
                fn(Expression $clause): string => $this->convertClauseToString($queryBuilderBinder, $clause),
                $expression->clauses,
            )
        );
    }

    private function convertClauseToString(ModelQueryBuilderBinder $queryBuilderBinder, Expression $clause): string
    {
        if ($clause instanceof CompositeExpression) {
            return $this->convertCompositeExpressionToString($queryBuilderBinder, $clause);
        }

        /** @var Filter $clause */
        return $this->convertSimpleExpressionToString($queryBuilderBinder, $clause);
    }
}