<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria\CompositeExpression;

use App\Shared\Domain\Criteria\Expression;

abstract readonly class CompositeExpression implements Expression
{
    public function __construct(
        public Type $type,
    ) {}
}
