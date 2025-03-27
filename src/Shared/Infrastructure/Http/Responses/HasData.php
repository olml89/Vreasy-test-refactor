<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Responses;

use App\Shared\Infrastructure\Presenter;

/**
 * @mixin JsonResponse
 */
trait HasData
{
    public function setData(Presenter $presenter): self
    {
        $this->body['data'] = $presenter->jsonSerialize();

        return $this;
    }
}