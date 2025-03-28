<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Responses;

use App\Shared\Domain\EntityNotFoundException;
use Tempest\Http\Status;

final class NotFound extends JsonResponse implements ErrorJsonResponse
{
    use HasErrors;

    public function __construct(EntityNotFoundException $throwable, bool $isDebug)
    {
        parent::__construct(Status::NOT_FOUND);

        $this->setErrorInformation($throwable, $isDebug);
    }
}