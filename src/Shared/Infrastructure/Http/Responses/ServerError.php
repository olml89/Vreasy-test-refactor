<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Responses;

use Tempest\Http\Status;
use Throwable;

final class ServerError extends JsonResponse implements ErrorJsonResponse
{
    use HasErrors;

    public function __construct(Throwable $e, bool $isDebug)
    {
        parent::__construct(Status::INTERNAL_SERVER_ERROR);
        $this->throwable = $e;

        $this->setErrorInformation($isDebug);
    }

    public function getGenericErrorMessage(): string
    {
        return 'Internal Server Error';
    }
}