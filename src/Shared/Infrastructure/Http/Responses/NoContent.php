<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Responses;

use Tempest\Http\Status;

final class NoContent extends JsonResponse
{
    public function __construct()
    {
        parent::__construct(Status::NO_CONTENT);

        $this->body = null;
    }
}