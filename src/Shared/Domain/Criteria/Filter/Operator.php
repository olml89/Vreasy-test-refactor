<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria\Filter;

enum Operator: string
{
    case EQ = 'eq';
    case NEQ = 'neq';
    case GT = 'gt';
    case GTE = 'gte';
    case LT = 'lt';
    case LTE = 'lte';
    case IN = 'in';
    case NIN = 'nin';
    case LIKE = 'like';

    public function comparesMultipleValues(): bool
    {
        return match ($this) {
            Operator::IN, Operator::NIN => true,
            default => false,
        };
    }
}
