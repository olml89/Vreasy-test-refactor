<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Responses;

use App\Shared\Infrastructure\Presenter;
use Tempest\Http\Status;

final class Created extends JsonResponse
{
    use HasData;

    public function __construct(Presenter $presenter)
    {
        parent::__construct(Status::CREATED);

        $this->setData($presenter);
    }
}