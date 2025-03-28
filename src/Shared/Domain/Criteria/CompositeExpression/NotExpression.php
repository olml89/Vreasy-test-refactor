<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria\CompositeExpression;

use App\Shared\Domain\Criteria\Expression;

final readonly class NotExpression extends CompositeExpression
{
    public function __construct(
        public Expression $clause,
    ) {
        parent::__construct(Type::NOT);
    }
}
