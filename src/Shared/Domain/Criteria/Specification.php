<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

interface Specification
{
    public function criteria(): Criteria;
}