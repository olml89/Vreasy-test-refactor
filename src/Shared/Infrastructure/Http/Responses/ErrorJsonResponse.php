<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Http\Responses;

interface ErrorJsonResponse
{
    public function getGenericErrorMessage(): ?string;
}