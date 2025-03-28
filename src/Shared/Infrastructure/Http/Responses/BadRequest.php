<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Responses;

use App\Shared\Domain\ValueObjectException;
use Tempest\Http\Status;

final class BadRequest extends JsonResponse implements ErrorJsonResponse
{
    use HasErrors;

    public function __construct(ValueObjectException $throwable, bool $isDebug)
    {
        parent::__construct(Status::BAD_REQUEST);

        $this->setErrorInformation($throwable, $isDebug);
    }
}