<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use RuntimeException;

abstract class DuplicatedEntityException extends RuntimeException
{
}