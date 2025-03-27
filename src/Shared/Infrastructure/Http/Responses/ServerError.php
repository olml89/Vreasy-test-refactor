<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Responses;

use Tempest\Http\Status;
use Throwable;

final class ServerError extends JsonResponse
{
    use HasErrors;

    public function __construct(Throwable $e, bool $isDebug)
    {
        parent::__construct(Status::INTERNAL_SERVER_ERROR);

        $this->setErrorInformation($e, $isDebug);
    }

    protected function genericErrorMessage(): string
    {
        return 'Internal Server Error';
    }
}