<?php declare(strict_types=1);

namespace App\Shared\Domain\Criteria\CompositeExpression;

enum Type: string
{
    case AND = 'and';
    case OR = 'or';
    case NOT = 'not';
}
