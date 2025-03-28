<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria\Filter;

final readonly class EqualTo extends Filter
{
    public function __construct(string $field, mixed $value)
    {
        parent::__construct(
            field: $field,
            operator: Operator::EQ,
            value: $value
        );
    }
}
