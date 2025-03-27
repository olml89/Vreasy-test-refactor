<?php

declare(strict_types=1);

namespace App\Shared\Http\Responses;

/**
 * @mixin JsonResponse
 */
trait HasData
{
    public function setData(array $data): self
    {
        $this->body['data'] = $data;

        return $this;
    }
}