<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\CompositeExpression\AndExpression;
use App\Shared\Domain\Criteria\CompositeExpression\CompositeExpression;
use App\Shared\Domain\Criteria\CompositeExpression\NotExpression;
use App\Shared\Domain\Criteria\CompositeExpression\OrExpression;
use App\Shared\Domain\Criteria\CompositeExpression\Type;
use App\Shared\Domain\Criteria\Filter\EqualTo;
use App\Shared\Domain\Criteria\Filter\Filter;
use App\Shared\Domain\Criteria\Filter\GreaterThan;
use App\Shared\Domain\Criteria\Filter\GreaterThanOrEqualTo;
use App\Shared\Domain\Criteria\Filter\In;
use App\Shared\Domain\Criteria\Filter\LessThan;
use App\Shared\Domain\Criteria\Filter\LessThanOrEqualTo;
use App\Shared\Domain\Criteria\Filter\Like;
use App\Shared\Domain\Criteria\Filter\NotEqualTo;
use App\Shared\Domain\Criteria\Filter\NotIn;
use App\Shared\Domain\Criteria\Filter\Operator;
use App\Shared\Domain\Criteria\Order\Order;

final class Criteria
{
    public function __construct(
        public Expression $expression,
        public ?Order $order = null,
        public ?int $offset = null,
        public ?int $limit = null,
    ) {}

    public static function buildCompositeExpression(Type $type, Expression ...$clauses): CompositeExpression
    {
        return match ($type) {
            Type::NOT => new NotExpression(...$clauses),
            Type::AND => new AndExpression(...$clauses),
            Type::OR => new OrExpression(...$clauses),
        };
    }

    public static function buildFilter(Operator $operator, string $field, mixed $value): Filter
    {
        return match($operator) {
            Operator::EQ => new EqualTo($field, $value),
            Operator::NEQ => new NotEqualTo($field, $value),
            Operator::LIKE => new Like($field, $value),
            Operator::GT => new GreaterThan($field, $value),
            Operator::GTE => new GreaterThanOrEqualTo($field, $value),
            Operator::LT => new LessThan($field, $value),
            Operator::LTE => new LessThanOrEqualTo($field, $value),
            Operator::IN => new In($field, $value),
            Operator::NIN => new NotIn($field, $value),
        };
    }
}