<?php declare(strict_types=1);

namespace App\Shared\Domain\Criteria\Order;

enum OrderType: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
}
