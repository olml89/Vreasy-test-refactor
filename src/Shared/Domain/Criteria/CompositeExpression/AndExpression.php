<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria\CompositeExpression;

use App\Shared\Domain\Criteria\Expression;

final readonly class AndExpression extends CompositeExpression
{
    /**
     * @var array<array-key, Expression>
     */
    public array $clauses;

    public function __construct(Expression ...$clauses)
    {
        $this->clauses = $clauses;

        parent::__construct(Type::AND);
    }
}
