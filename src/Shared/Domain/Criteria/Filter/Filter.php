<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria\Filter;

use App\Shared\Domain\Criteria\Expression;
use InvalidArgumentException;

abstract readonly class Filter implements Expression
{
    public function __construct(
        public string $field,
        public Operator $operator,
        public mixed $value,
    ) {
        if (is_array($value) && !$this->operator->comparesMultipleValues()) {
            throw new InvalidArgumentException(
                sprintf(
                    'Operator %s cannot compare multiple values.',
                    $this->operator->value,
                )
            );
        }
    }
}