<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Responses;

use App\Shared\Domain\DuplicatedEntityException;
use Tempest\Http\Status;

final class Conflict extends JsonResponse implements ErrorJsonResponse
{
    use HasErrors;

    public function __construct(DuplicatedEntityException $throwable, bool $isDebug)
    {
        parent::__construct(Status::CONFLICT);

        $this->setErrorInformation($throwable, $isDebug);
    }
}