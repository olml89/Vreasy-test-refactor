<?php

declare(strict_types=1);

namespace App\Shared\Http\Responses;

use Tempest\Http\Status;

final class Created extends JsonResponse
{
    use HasData;

    public function __construct(array $body)
    {
        parent::__construct(Status::CREATED);

        $this->setData($body);
    }
}