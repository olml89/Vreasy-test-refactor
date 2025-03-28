<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Database;

use App\Shared\Domain\Criteria\Filter\Filter;
use App\Shared\Domain\Criteria\Filter\Operator;
use Tempest\Database\Builder\FieldName;
use Tempest\Database\Builder\ModelDefinition;
use Tempest\Database\Builder\ModelQueryBuilder;

final class ModelQueryBuilderBinder
{
    /**
     * @var array<string, mixed>
     */
    private array $bindingCounts = [];

    public function __construct(
        private readonly ModelDefinition $modelDefinition,
        private readonly ModelQueryBuilder $queryBuilder,
    ) {}

    public function query(): ModelQueryBuilder
    {
        return $this->queryBuilder;
    }

    public function bindExpression(Filter $expression): string
    {
        $fieldName = $this->modelDefinition->getFieldName($expression->field);

        return $expression->operator->comparesMultipleValues()
            ? $this->bindMultipleValuesExpression($fieldName, $expression)
            : $this->bindSingleValueExpression($fieldName, $expression);
    }

    private function bindSingleValueExpression(FieldName $fieldName, Filter $expression): string
    {
        $bindingName = $this->getBindingName($fieldName);

        $stringifiedExpression = sprintf(
            '%s %s :%s',
            $fieldName,
            $this->convertOperator($expression->operator),
            $bindingName,
        );

        $value = $expression->operator === Operator::LIKE
            ? '%' . $expression->value . '%'
            : $expression->value;

        $this->queryBuilder->bind(...[$bindingName => $value]);

        return $stringifiedExpression;
    }

    private function bindMultipleValuesExpression(FieldName $fieldName, Filter $expression): string
    {
        $bindings = [];

        foreach ($expression->value as $value) {
            $bindings[$this->getBindingName($fieldName)] = $value;
        }

        $stringifiedExpression = sprintf(
            '%s %s (%s)',
            $fieldName,
            $this->convertOperator($expression->operator),
            implode(
                ', ',
                array_map(
                    fn (string $bindingName): string => sprintf(':%s', $bindingName),
                    array_keys($bindings)
                ),
            )
        );

        $this->queryBuilder->bind(...$bindings);

        return $stringifiedExpression;
    }

    private function getBindingName(FieldName $fieldName): string
    {
        $bindingName = $fieldName->fieldName;

        if (!array_key_exists($bindingName, $this->bindingCounts)) {
            $this->bindingCounts[$bindingName] = 0;
        }

        ++$this->bindingCounts[$bindingName];

        if ($this->bindingCounts[$bindingName] ?? 0 > 1) {
            $bindingName .= $this->bindingCounts[$bindingName];
        }

        return $bindingName;
    }

    private function convertOperator(Operator $operator): string
    {
        return match ($operator) {
            Operator::EQ => '=',
            Operator::NEQ => '<>',
            Operator::GT => '>',
            Operator::GTE => '>=',
            Operator::LT => '<',
            Operator::LTE => '<=',
            Operator::IN => 'IN',
            Operator::NIN => 'NOT IN',
            Operator::LIKE => 'LIKE',
        };
    }
}